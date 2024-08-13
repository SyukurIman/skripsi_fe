<script>
    var data = function (){
        var dt = new Date();
        var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();


        var msg = function(){
            @if(session()->get('status'))
                swal.fire({
                    title: "Success",
                    text : '{{ session()->get('status') }}',
                    confirmButtonColor: '#EF5350',
                    type: "success"
                })
            @endif

            @if(session()->get('errors'))
                swal.fire({
                    title: "Error",
                    text : '{{ session()->get('errors') }}',
                    confirmButtonColor: '#EF5350',
                    type: "dangger"
                })
            @endif


        }


        return {
            init: function(){
                msg();
            }
        }
    }();

    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.fn.dataTable.ext.errMode = 'none';
        data.init();
    });
</script>
