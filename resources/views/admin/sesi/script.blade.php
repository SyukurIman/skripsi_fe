<script>
    var data = function (){
        var dt = new Date();
        var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();

        var table = function(){
            var t = $('#table').DataTable({
                processing: true,
                pageLength: 10,
                serverSide: true,
                searching: true,
                bLengthChange: true,
                lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "Semua"] ],
                destroy : true,
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excel',
                        title: 'Data Layanan - ' + time,
                        text: '<i class="fa fa-file-excel-o"></i> Cetak',
                        titleAttr: 'Cetak',
                        exportOptions: {
                            columns: ':visible',
                            modifier: {
                                page: 'current'
                            }
                        }
                    },
                ],
                'ajax': {
                    "url": "/admin/data_sesi/{{ $id_layanan }}",
                    "method": "POST",
                    "complete": function () {
                        $('.buttons-excel').hide();
                        swal.close();
                    }
                },
                'columns': [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', class: 'text-center', orderable: false, searchable: false },
                    { data: 'action', name: 'action', class: 'text-center', orderable: false, searchable: false },
                    { data: 'durasi', name: 'durasi', class: 'text-left'},
                    { data: 'tanggal', name: 'tanggal', class: 'text-left' },
                    { data: 'waktu_mulai', name: 'waktu_mulai', class: 'text-left' },
                    { data: 'waktu_selesai', name: 'waktu_selesai', class: 'text-left' },
                    { data: 'status_sesi', name: 'status_sesi', class: 'text-left' },

                ],
                "order": [],
                "columnDefs": [
                    { "orderable": false, "targets": [0] }
                ],
                "language": {
                    "lengthMenu": "Menampilkan _MENU_ data",
                    "search": "Cari:",
                    "zeroRecords": "Data tidak ditemukan",
                    "paginate": {
                        "first":      "Pertama",
                        "last":       "Terakhir",
                        "next":       "Selanjutnya",
                        "previous":   "Sebelumnya"
                    },
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                    "infoEmpty": "Data kosong",
                    "infoFiltered": "(Difilter dari _MAX_ total data)"
                }
            });

            filterKolom(t);
            hideKolom(t);
            cetak(t);
        }

        var filterKolom = function(t){
            $('.toggle-vis').on('change', function (e) {
                e.preventDefault();
                var column = t.column($(this).attr('data-column'));
                column.visible(!column.visible());
            });
        }

        var hideKolom = function(t){
            var arrKolom = [];
            $('.toggle-vis').each(function(i, value){
                if(!$(value).is(":checked")){
                    arrKolom.push(i+2);
                }
            });
            arrKolom.forEach(function(val){
                var column = t.column(val);
                column.visible(!column.visible());
            });
        }

        var cetak = function(t){
            $("#btn-cetak").on("click", function() {
                t.button('.buttons-excel').trigger();
            });
        }

        var muatUlang = function(){
            $('#btn-muat-ulang').on('click', function(){
                $('#table').DataTable().ajax.reload();
            });
        }

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

        var hapus = function() {
            $('#table').on('click', '#btn-hapus', function() {
                var baris = $(this).parents('tr')[0];
                var table = $('#table').DataTable();
                var data = table.row(baris).data();

                swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: 'Menghapus Data Ini',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#2196F3',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak'
                    })
                    .then((result) => {
                        if (result.value) {
                            var fd = new FormData();
                            fd.append('_token', '{{ csrf_token() }}');
                            fd.append('id', data.id);

                            $.ajax({
                                url: "{{ route('admin.layanan.delete') }}",
                                type: "POST",
                                data: fd,
                                dataType: "json",
                                contentType: false,
                                processData: false,
                                beforeSend: function() {
                                    swal.fire({
                                        html: '<h5>Loading...</h5>',
                                        showConfirmButton: false
                                    });
                                },
                                success: function(result) {
                                    swal.fire({
                                        title: result.title,
                                        text: result.text,
                                        confirmButtonColor: result.ButtonColor,
                                        type: result.type,
                                    });

                                    if (result.type == 'success') {
                                        swal.fire({
                                            title: result.title,
                                            text: result.text,
                                            confirmButtonColor: result.ButtonColor,
                                            type: result.type,
                                        }).then((result) => {
                                            $('#table').DataTable().ajax.reload();
                                        });
                                    } else {
                                        swal.fire({
                                            title: result.title,
                                            text: result.text,
                                            confirmButtonColor: result.ButtonColor,
                                            type: result.type,
                                        });
                                    }
                                }
                            });
                        } else {
                            swal.fire({
                                text: 'Aksi Dibatalkan!',
                                type: "info",
                                confirmButtonColor: "#EF5350",
                            });
                        }
                    });
            });
        }

        return {
            init: function(){
                @if($position == "Data Sesi")
                    table();
                    muatUlang();
                    hapus();
                @endif

                msg();
                // set_dokter();

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
