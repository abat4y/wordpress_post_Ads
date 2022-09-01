jQuery(document).ready( function() {
    jQuery(".Advert_count").click( function(e) {
       //e.preventDefault(); 
       post_id = jQuery(this).attr("data-post_id");
       nonce = jQuery(this).attr("data-nonce");
       link = jQuery(this).attr('href');
       jQuery.ajax({
         type: "post",
         dataType: "json",
         url: my_ajax_object.ajax_url,
         data : {action: "get_data", post_id : post_id, nonce: nonce},
         success: function(response){
             console.log(link);
         },
         error: function (response) {
         }
     });
    });
 });