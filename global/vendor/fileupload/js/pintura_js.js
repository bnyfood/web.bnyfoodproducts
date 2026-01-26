
      // import Pintura Image Editor functionality:
      import { openDefaultEditor } from './global/vendor/fileupload/js/vendor/pintura.min.js';
      $(function () {
        $('#fileupload').fileupload('option', {
          // When editing a file use Pintura Image Editor:
          edit: function (file) {
            return new Promise((resolve, reject) => {
              const editor = openDefaultEditor({
                src: file,
                imageCropAspectRatio: 1,
              });
              editor.on('process', ({ dest }) => {
                resolve(dest);
              });
              editor.on('close', () => {
                resolve(file);
              });
            });
          }
        });
      });