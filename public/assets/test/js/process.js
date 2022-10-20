$(function() {
    //$('table').DataTable();
    //créer une campagne
    $('body').on('click','.editBtn', function(e){
        e.preventDefault();
        $.ajax({
            url: '/{id: this.data.id}/edit',
            type: 'post',
            data: 'campagneid: this.data.id',
            success: function(response) {
                console.log(response)
            }
        })
    });
});