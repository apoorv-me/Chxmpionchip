
  function deleteUser(id){
    var token = $('#token').val();
    var requestdelete = $('#delete-url').val().trim();

    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this User",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
          $.ajax({
            url:requestdelete,
            type: "POST",
            data: 'id='+id+'&_token='+token,
            success: function(response){
            if(response.success===true){
            $('#'+id).closest('tr').remove();
                }
              }
            });
      } else {
        swal("User is safe");
      }
    });
  }



  


  function updateStatus(id){
    var token = $('#token').val();
    var request = $('#status-url').val().trim();

    swal({
      title: "Are you sure?",
      text: "You want to change the status of this User !",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
          $.ajax({
            url:request,
            type: "POST",
            data: 'id='+id+'&_token='+token,
            success: function(response){
            if(response.success===true){
              swal("Good job!", "Status Changed Successfully","success").then( () => {
                location.reload();
            })
                }
              }
            });
      } else {
        swal("Status is not change");
      }
    });
  }



 