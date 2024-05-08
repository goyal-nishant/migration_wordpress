
console.log("This is before function calling");
function otherScriptFunction(productId, customPrice, customCategory) {
  console.log("Received Product ID:", productId);
  console.log("Received Custom Price:", customPrice);
  console.log("Received Custom Price:", customCategory);
  jQuery("#product-info").html(
    "Product ID: " + productId + ", Custom Price: " + customPrice +", customCategory: " + customCategory  
  );
}
console.log("this page works");
