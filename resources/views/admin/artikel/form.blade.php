<style>
  .container_file {
        height: 300px;
        width: 300px;
        border-radius: 10px;
        box-shadow: 4px 4px 30px rgba(0, 0, 0, .2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        gap: 5px;
        background-color: rgba(0, 110, 255, 0.041);
    }

    .header_file {
        flex: 1;
        width: 100%;
        border: 2px dashed royalblue;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .header_file svg {
        height: 100px;
    }

    .header_file img {
        max-width: 230px; 
        max-height: 230px; 
        width: auto; 
        height: auto; 
    }


    .header_file p {
        text-align: center;
        color: black;
    }

    .footer_file {
        background-color: rgba(0, 110, 255, 0.075);
        width: 100%;
        height: 40px;
        padding: 8px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        color: black;
        border: none;
    }

    .footer_file svg {
        height: 130%;
        fill: royalblue;
        background-color: rgba(70, 66, 66, 0.103);
        border-radius: 50%;
        padding: 2px;
        cursor: pointer;
        box-shadow: 0 2px 30px rgba(0, 0, 0, 0.205);
    }

    .footer_file p {
        flex: 1;
        margin-top: 15px;
        text-align: center;
    }

    #file {
        display: none;
    }

</style>

<div class="col mb-lg-0 mb-4 px-4">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col">
            <div class="d-flex flex-column h-100">
                <a class="text-body text-sm font-weight-bold mb-0 icon-move-right mt-auto mb-4" href="{{ route('artikel.index') }}" >
                    <i class="fas fa-arrow-left text-sm ms-1" aria-hidden="true"></i>
                      Kembali
                    </a>
                <h5 class="font-weight-bolder">{{ $position }}</h5>


                <form id="form-data" method="post" autocompleted="off" enctype="multipart/form-data">
                    {{csrf_field();}}
                    <div class="form-group row px-2">
                          <div class="col-md-12 mb-4">
                            <div class="d-flex justify-content-center">
                                <div class="container_file"> 
                                <div class="header_file"> 
                                    @if ($type == 'create')
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> 
                                    <path d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10C19.2091 10 21 11.7909 21 14C21 15.4806 20.1956 16.8084 19 17.5M7 10C4.79086 10 3 11.7909 3 14C3 15.4806 3.8044 16.8084 5 17.5M7 10C7.43285 10 7.84965 10.0688 8.24006 10.1959M12 12V21M12 12L15 15M12 12L9 15" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg> <p>Browse img to upload!</p>
                                    @else
                                    <img src="{{ '/storage/image/artikel/' . $data_artikel->link_gambar }}" alt="">
                                    @endif
                                </div> 
                                <input type="hidden" name="id" value="{{$type == 'update' ? $data_artikel->id: ''}}">
                                <input type="file" class="form-control fileImg" name="link_gambar"> 
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mt1">
                            <label for="judul">Judul </label>
                            <input class="form-control input_form" type="text" name="judul" id="" value="{{$type == 'update' ? $data_artikel->judul:''}}" required>
                        </div>
                        <div class="col-6 mt1">
                            <label for="nama_penulis">Penulis </label>
                            <input class="form-control input_form" type="text" name="nama_penulis" id="" value="{{$type == 'update' ? $data_artikel->nama_penulis: ''}}" required>
                        </div>
                        <div class="col-12 mt1">
                          <label for="deskripsi">Deskripsi </label>
                          <textarea class="form-control input_form" type="text" name="deskripsi" required>{{$type == 'update' ? $data_artikel->deskripsi:''}}</textarea>
                        </div>
                    </div>
                    <div class="text-end">
                      <button type="button" class="btn btn-primary" id="simpan">
                        SUBMIT
                      </button>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


