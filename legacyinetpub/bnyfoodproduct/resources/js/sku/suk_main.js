$(document).ready(function() {
	const ProductSkuStore = JSON.parse(
      localStorage.getItem("ProductSkuStore")
    );

    $("#sku_name").val(ProductSkuStore.sku_name);
});