import sys
import json
import re
import pymupdf

TESSERACT_CMD = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

def extract_text_normal(page):
    return page.get_text()

def extract_text_ocr(page):
    try:
        import pytesseract
        from PIL import Image
        import io

        pytesseract.pytesseract.tesseract_cmd = TESSERACT_CMD

        pix = page.get_pixmap(dpi=300)
        img_data = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_data))

        text = pytesseract.image_to_string(img, lang='eng', config='--psm 6')
        return text
    except Exception:
        return ''

def extract_receipt_number_ocr(page):
    try:
        import pytesseract
        from PIL import Image
        import io

        pytesseract.pytesseract.tesseract_cmd = TESSERACT_CMD

        rect = page.rect
        top_clip = pymupdf.Rect(rect.width * 0.4, 0, rect.width, rect.height * 0.25)
        pix = page.get_pixmap(dpi=350, clip=top_clip)
        img_data = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_data))

        text = pytesseract.image_to_string(img, lang='eng', config='--psm 6')
        return text
    except Exception:
        return ''

def extract_amount_ocr(page):
    try:
        import pytesseract
        from PIL import Image
        import io

        pytesseract.pytesseract.tesseract_cmd = TESSERACT_CMD

        rect = page.rect
        bottom_clip = pymupdf.Rect(rect.width * 0.3, rect.height * 0.7, rect.width, rect.height)
        pix = page.get_pixmap(dpi=350, clip=bottom_clip)
        img_data = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_data))

        text = pytesseract.image_to_string(img, lang='eng', config='--psm 6')
        return text
    except Exception:
        return ''

def parse_receipt(text, receipt_text_extra='', amount_text_extra=''):
    receipt_number = ''
    total_amount = ''

    combined_receipt = text + '\n' + receipt_text_extra

    m = re.search(r'(TTSTHAC\d{13,})', combined_receipt)
    if not m:
        m = re.search(r'(TTSTHAC\d{10,})', combined_receipt, re.IGNORECASE)
    if not m:
        m = re.search(r'(TTS[A-Z]{2,4}\d{10,})', combined_receipt, re.IGNORECASE)
    if m:
        receipt_number = m.group(1).upper()

    combined_amount = text + '\n' + amount_text_extra

    patterns = [
        r'Total\s*Amount[\s\S]{0,80}?([\d,]+\.\d{2})',
        r'Net\s*payable\s*amount[\s\S]{0,80}?([\d,]+\.\d{2})',
    ]
    for pattern in patterns:
        m = re.search(pattern, combined_amount, re.IGNORECASE)
        if m:
            val = m.group(1).replace(',', '')
            if float(val) > 0:
                total_amount = val
                break

    return receipt_number, total_amount

def extract_single_file(pdf_path, first_page_only=True):
    """Extract receipt from one PDF file. Returns dict or None."""
    try:
        doc = pymupdf.open(pdf_path)
        if doc.page_count == 0:
            doc.close()
            return None

        use_ocr = False
        first_text = extract_text_normal(doc[0])
        if not re.search(r'TTSTHAC|TTS[A-Z]', first_text, re.IGNORECASE):
            use_ocr = True

        page_count = 1 if first_page_only else doc.page_count

        for page_num in range(page_count):
            page = doc[page_num]

            if use_ocr:
                receipt_text = extract_receipt_number_ocr(page)
                amount_text = extract_amount_ocr(page)
                full_text = extract_text_ocr(page)
                receipt_number, total_amount = parse_receipt(full_text, receipt_text, amount_text)
            else:
                text = extract_text_normal(page)
                receipt_number, total_amount = parse_receipt(text)

            if receipt_number:
                doc.close()
                return {
                    'receipt_number': receipt_number,
                    'total_amount': total_amount
                }

        doc.close()
    except Exception:
        pass

    return None

def extract_batch(file_paths):
    results = []
    for pdf_path in file_paths:
        item = extract_single_file(pdf_path, first_page_only=True)
        if item:
            results.append(item)
    return results

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'No input provided'}))
        sys.exit(1)

    arg1 = sys.argv[1]

    if arg1 == '--batch' and len(sys.argv) >= 3:
        list_file = sys.argv[2]
        try:
            with open(list_file, 'r', encoding='utf-8') as f:
                file_paths = json.load(f)
            results = extract_batch(file_paths)
            print(json.dumps(results, ensure_ascii=False))
        except Exception as e:
            print(json.dumps({'error': str(e)}))
            sys.exit(1)
    else:
        item = extract_single_file(arg1, first_page_only=False)
        if item:
            print(json.dumps([item], ensure_ascii=False))
        else:
            print(json.dumps([]))
