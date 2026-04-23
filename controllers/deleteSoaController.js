function deleteSoa(soaid){


var confirmation= confirm('Are you sure you want to delete this SOA?');
if(confirmation){
    var fd = new FormData();
    fd.append('soaid', soaid);
    $.ajax({
        url: "services/deleteSoaService.php",
        data: fd,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (result) {
            result=result.trim();
            if (result == "success") {
                alert("Soa was successfully deleted.");
               location.reload();
             }else {
                alert("An error has occurred. Please try again");
            }
        }
    });

}
 


}