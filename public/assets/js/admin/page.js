function getStaticPagesData(pageId){
	if(pageId==null || pageId==''){
		alert('Please select a page');
		return false;
	}
	$('#textarealabel').css('display','block');
	$('.error').html('');
	$('#successDisplay').css('display','none');
	var requesturl = $('#base-url').val().trim();
	$('.latin__loader-container').addClass("show");
	$.ajax({
    url:requesturl+'/admin/get-content/'+pageId,
    type: "GET",
    success: function(response){
		$('.latin__loader-container').removeClass("show");
		if(response.success==true){
		$('#page_id').val(pageId);
		CKEDITOR.instances['content'].setData(response.data[0]['content']);
		} else {
			$('#page_id').val(pageId);
			CKEDITOR.instances['content'].setData('');	
		}
	}
      });
		}

		// $(document).ready(function (){
		// 	var pageId = $('#page_id').val().trim();
		// 	console.log('hello');
		// 	console.log(pageId+'dsdsd');
		//   });