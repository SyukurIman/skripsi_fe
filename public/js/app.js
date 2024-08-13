let meetingJoined = false;
const meeting     = new Metered.Meeting();
let cameraOn      = false;
let micOn         = false;

let screenSharingOn   = false;
let localVideoStream  = null;
let activeSpeakerId   = null;
let meetingInfo       = {};
let status_pengguna   = '';
let status_send       = false
let loop_status       = false
let video_capture     = true

let classificationResultContainer = document.getElementById('classificationResult');
async function initializeView() {

    // Kamera Setting
    const videoInputDevices  = await meeting.listVideoInputDevices();
    const videoOptions       = [];

    for (let item of videoInputDevices) {
        videoOptions.push(
            `<option value="${item.deviceId}">${item.label}</option>`
        )
     }
    $("#cameraSelectBox").html(videoOptions.join(""));

    // Audio Seeting
    const audioInputDevices = await meeting.listAudioInputDevices();
    const audioOptions      = [];

    for (let item of audioInputDevices) {
        audioOptions.push(
            `<option value="${item.deviceId}">${item.label}</option>`
        )
    }
    $("#microphoneSelectBox").html(audioOptions.join(""));


    $("#waitingAreaToggleMicrophone").on("click", function() {
        if (micOn) {
            micOn = false;
            $("#waitingAreaToggleMicrophone").removeClass("bg-gray-500");
            $("#waitingAreaToggleMicrophone").addClass("bg-gray-400");
        } else {
            micOn = true;
            $("#waitingAreaToggleMicrophone").removeClass("bg-gray-400");
            $("#waitingAreaToggleMicrophone").addClass("bg-gray-500");
        }
    });

    $("#waitingAreaToggleCamera").on("click", async function() {
        if (cameraOn) {
            cameraOn = false;
            $("#waitingAreaToggleCamera").removeClass("bg-gray-500");
            $("#waitingAreaToggleCamera").addClass("bg-gray-400");

            const tracks = localVideoStream.getTracks();
            tracks.forEach(function (track) {
              track.stop();
            });

            localVideoStream = null;
            $("#waitingAreaLocalVideo")[0].srcObject = null;
        } else {
            cameraOn = true;
            $("#waitingAreaToggleCamera").removeClass("bg-gray-400");
            $("#waitingAreaToggleCamera").addClass("bg-gray-500");

            localVideoStream = await meeting.getLocalVideoStream();
            $("#waitingAreaLocalVideo")[0].srcObject = localVideoStream;
            cameraOn = true;
        }
    });

    /**
     * Adding Event Handlers
     */
    $("#cameraSelectBox").on("change", async function() {
        const deviceId = $("#cameraSelectBox").val();
        await meeting.chooseVideoInputDevice(deviceId);

        if (cameraOn) {
            localVideoStream = await meeting.getLocalVideoStream();
            $("#waitingAreaLocalVideo")[0].srcObject = localVideoStream;
        }
    });

    $("#microphoneSelectBox").on("change", async function() {
        const deviceId = $("#microphoneSelectBox").val();
        await meeting.chooseAudioInputDevice(deviceId);
    });
}
initializeView();


$("#joinMeetingBtn").on("click", async function () {
    var key = $("#id_key").val();

    if (!key) {
      return alert("Mohon isi Metting ID!!");
    }

    let data = validasi_room(key)
    data.then(async result => {
      console.log(result)
      if (result.status != 'Gagal') {
        window.MEETING_ID     = result.kunci_room;
        window.METERED_DOMAIN = result.domain;
        status_pengguna       = result.status

        try {
            meetingInfo = await meeting.join({
                roomURL: `${window.METERED_DOMAIN}/${window.MEETING_ID}`,
                name: result.username,
            });

            console.log("Meeting joined", meetingInfo);
            $("#waitingArea").addClass("hidden");
            $("#meetingView").removeClass("hidden");

            $("#meetingAreaUsername").text(result.username);
            $('#t_room_name').text('Room - '+result.jenis_fitur)

            if (result.jenis_fitur == "Call") {
                video_capture = false
                $('#toggleCamera').attr('hidden', true);
            } else {
            if (cameraOn) {
                await meeting.startVideo();
                $("#localVideoTag")[0].srcObject = localVideoStream;
                $("#localVideoTag")[0].play();
                $("#toggleCamera").removeClass("bg-gray-400");
                $("#toggleCamera").addClass("bg-gray-500");
            }
            }

            if (micOn) {
                $("#toggleMicrophone").removeClass("bg-gray-400");
                $("#toggleMicrophone").addClass("bg-gray-500");
                await meeting.startAudio();
            }
        } catch (error) {
            alert(error.message)
            console.log(error.message)
        }
      } else {
        alert(result.pesan)
      }

      var key         = $("#id_key").val();
      let data_image  = get_image(key);
      data_image.then(blob => {
        if (status_pengguna == 'Pelanggan') {
          setBackgroundImage('#localParticiapntContainer', blob.image_user)
        } else {
          setBackgroundImage('#localParticiapntContainer', blob.image_dokter)
        }
      })

    })
    validasi()

  });

  meeting.on("onlineParticipants", async function(participants) {
    if (participants.length >= 2) {
        $("#waiting_participant").addClass("hidden");
    }

    for (let participantInfo of participants) {
      if (!$(`#participant-${participantInfo._id}`)[0] && participantInfo._id !== meeting.participantInfo._id) {
        $("#remoteParticipantContainer").append(
          `
          <div id="participant-${participantInfo._id}" class="participant_c rounded-3xl bg-gray-900 relative">
            <video id="video-${participantInfo._id}" src="" autoplay class="object-contain w-full stream_part rounded-t-3xl"></video>
            <video id="audio-${participantInfo._id}" src="" autoplay class="hidden"></video>
            <div class="text_video absolute h-8 w-full bg-gray-700 rounded-b-3xl bottom-0 text-white text-center font-bold pt-1">
                ${participantInfo.name}
            </div>
          </div>
          `
        );

        var key     = $("#id_key").val();
        let data_image = get_image(key);
        data_image.then(blob => {
            if (status_pengguna != 'Pelanggan') {
              setBackgroundImage(`#participant-${participantInfo._id}`, blob.image_user)
            } else {
              setBackgroundImage(`#participant-${participantInfo._id}`, blob.image_dokter)
            }
          })
      }
    }
  });

  meeting.on("participantLeft", function(participantInfo) {
    $("#participant-" + participantInfo._id).remove();
    $("#waiting_participant").removeClass("hidden");

    if (participantInfo._id === activeSpeakerId) {
      $("#activeSpeakerUsername").text("");
      $("#activeSpeakerUsername").addClass("hidden");
    }
  });

  meeting.on("remoteTrackStarted", async function(remoteTrackItem) {
    if (remoteTrackItem.type === "video" && video_capture == true) {
      let mediaStream = new MediaStream();
      mediaStream.addTrack(remoteTrackItem.track);
      if ($("#video-" + remoteTrackItem.participantSessionId)[0]) {
        $("#video-" + remoteTrackItem.participantSessionId)[0].srcObject = mediaStream;
        $("#video-" + remoteTrackItem.participantSessionId)[0].play();
      }
    }

    if (remoteTrackItem.type === "audio") {
      let mediaStream = new MediaStream();
      mediaStream.addTrack(remoteTrackItem.track);
      if ( $("#video-" + remoteTrackItem.participantSessionId)[0]) {
        $("#audio-" + remoteTrackItem.participantSessionId)[0].srcObject = mediaStream;
        $("#audio-" + remoteTrackItem.participantSessionId)[0].play();
      }
    }

    if (status_pengguna != 'Pelanggan') {
      loop_status = true
      await loop_data(loop_status)
    }
  });

  meeting.on("remoteTrackStopped", async function(remoteTrackItem) {
    if (remoteTrackItem.type === "video" && video_capture == true) {
      if ( $("#video-" + remoteTrackItem.participantSessionId)[0]) {
        $("#video-" + remoteTrackItem.participantSessionId)[0].srcObject = null;
        $("#video-" + remoteTrackItem.participantSessionId)[0].pause();
      }

      if (remoteTrackItem.participantSessionId === activeSpeakerId) {
        $("#activeSpeakerVideo")[0].srcObject = null;
        $("#activeSpeakerVideo")[0].pause();
      }
    }

    if (remoteTrackItem.type === "audio") {
      if ($("#audio-" + remoteTrackItem.participantSessionId)[0]) {
        $("#audio-" + remoteTrackItem.participantSessionId)[0].srcObject = null;
        $("#audio-" + remoteTrackItem.participantSessionId)[0].pause();
      }
    }

    if (status_pengguna == 'psikolog') {
      loop_status = false
      await loop_data(loop_status)
    }

  });

  function setActiveSpeaker(activeSpeaker) {
    if (activeSpeakerId  != activeSpeaker.participantSessionId) {
      $(`#participant-${activeSpeakerId}`).show();
    }

    activeSpeakerId = activeSpeaker.participantSessionId;
    $(`#participant-${activeSpeakerId}`).hide();

    $("#activeSpeakerUsername").text(activeSpeaker.name || activeSpeaker.participant.name);

    if ($(`#video-${activeSpeaker.participantSessionId}`)[0]) {
      let stream = $(
        `#video-${activeSpeaker.participantSessionId}`
      )[0].srcObject;
      $("#activeSpeakerVideo")[0].srcObject = stream.clone();
    }

    if (activeSpeaker.participantSessionId === meeting.participantSessionId) {
      let stream = $(`#localVideoTag`)[0].srcObject;
      if (stream) {
        $("#localVideoTag")[0].srcObject = stream.clone();
      }
    }
  }

  $("#toggleMicrophone").on("click",  async function() {
    if (micOn) {
      $("#toggleMicrophone").removeClass("bg-gray-500");
      $("#toggleMicrophone").addClass("bg-gray-400");
      micOn = false;
      await meeting.stopAudio();
    } else {
      $("#toggleMicrophone").removeClass("bg-gray-400");
      $("#toggleMicrophone").addClass("bg-gray-500");
      micOn = true;
      await meeting.startAudio();
    }
  });


  $("#toggleCamera").on("click",  async function() {
    if (cameraOn) {
      $("#toggleCamera").removeClass("bg-gray-500");
      $("#toggleCamera").addClass("bg-gray-400");
      $("#toggleScreen").removeClass("bg-gray-500");
      $("#toggleScreen").addClass("bg-gray-400");
      cameraOn = false;
      await meeting.stopVideo();

      const tracks = localVideoStream.getTracks();
      tracks.forEach(function (track) {
        track.stop();
      });

      localVideoStream    = null;
      $("#localVideoTag")[0].srcObject = null;

      status_send = false
      if (status_pengguna == 'Pelanggan') {
        loop_status = false
        await loop_data(loop_status)
      }
    } else {
      $("#toggleCamera").removeClass("bg-gray-400");
      $("#toggleCamera").addClass("bg-gray-500");
      cameraOn = true;
      await meeting.startVideo();
      localVideoStream = await meeting.getLocalVideoStream();
      $("#localVideoTag")[0].srcObject = localVideoStream;

      status_send = true
      if (status_pengguna == 'Pelanggan') {
        loop_status = true
        await loop_data(loop_status)
      }
    }
  });

  $("#leaveMeeting").on("click", async function() {
    await meeting.leaveMeeting();
    $("#meetingView").addClass("hidden");
    $("#leaveMeetingView").removeClass("hidden");
  });

async function captureAndSendFrame() {
    if (!cameraOn || !localVideoStream) return;

    const video = document.createElement('video');
    video.srcObject = localVideoStream;
    video.play();

    video.onloadeddata = async function() {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64Image = canvas.toDataURL('image/png');
        sendImageToApi(base64Image);

    };
}

// Fungsi untuk mengirim data gambar ke API
async function sendImageToApi(base64Image) {
    try {
        const response = await fetch('https://dp8r6t41-5000.asse.devtunnels.ms/get_image', {
            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ imageData: base64Image })
        });
        const result = await response.json();

        return result;
    } catch (error) {
        console.error('Error sending image to API:', error);
        return null;
    }
}

function setResult(message) {
  classificationResultContainer.innerText = `Hasil Prediksi: ${message}`;
}

async function loop_data() {
  if (loop_status == true && video_capture == true) {
      if (status_pengguna == 'Pelanggan' && status_send == true) {
        captureAndSendFrame()
      } else {
        let result_image = await get_info();
        setResult(result_image);

        $("#classificationResult").removeClass("hidden");
        $("#classificationResult").addClass("flex");
      }

      setTimeout(loop_data, 10000);
  } else {
    $("#classificationResult").addClass("hidden");
  }
}

async function validasi(){
  var key             = $("#id_key").val();
  let status_validasi = validasi_room(key);

  status_validasi.then(async result => {
    if (result.status == "Gagal") {
      loop_status = false
      if ($('#meetingView.hidden').length) {
        console.log("Di Lobby")
      } else {
        await meeting.leaveMeeting();
        $("#meetingView").addClass("hidden");
        $("#leaveMeetingView").removeClass("hidden");
      }
    } else {
      setTimeout(validasi, 60000);
    }
  })

}

async function get_info() {
  try {
      const response = await fetch('https://dp8r6t41-5000.asse.devtunnels.ms/get_result', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
      });

      let result  = await response.json();
      let text = result.label+""
      text = text.replace(',', " | ")
      text = text.replace(',', " | ")
      text = text.replace(',', " | ")
      return text.replace(',', " | ");
  } catch (error) {
      console.error('Error sending image to API:', error);
      return null;
  }
}

async function get_image(key) {
  try {
      const response = await fetch('/api/v1/room/image', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'Authorization': 'Bearer '+window.localStorage.getItem('token')
          },
          body: JSON.stringify({ number_key: key })
      });

      const result  = await response.json();
      return result;
  } catch (error) {
      console.error('Error image not found:', error);
      return null;
  }
}

async function validasi_room(key){
  try {
    const response = await fetch('/api/v1/room/validasi', {
      method: 'POST',

      headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer '+window.localStorage.getItem('token')
      },
      body: JSON.stringify({ number_key: key })
    });
    const result = await response.json();

    if (result.status_token == "Diperbarui") {
        window.localStorage.removeItem('token')
        window.localStorage.setItem('token', result.token)
    }

    return result
  } catch (error) {
    console.error('Error Validasi to API:', error);
    return null
  }
}

function setBackgroundImage(elementSelector, base64String) {
  document.querySelector(elementSelector).style.backgroundImage = `url(${base64String})`;
}

async function cek_login() {
    const response = await fetch('/api/v1/image_profile', {
      method: 'GET',

      headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer '+window.localStorage.getItem('token')
      },
    });

    const result = await response.json();

    if (result.status_token == "Diperbarui") {
        window.localStorage.removeItem('token')
        window.localStorage.setItem('token', result.token)
    }

    if (result.status != "Berhasil") {
        window.location.href = '/';
    } else {
        setBackgroundImage('#waitingAreaLocalVideo', result.image_profile)
    }
}
cek_login()

$('#btn_logout').on('click',async function () {
    const response = await fetch('/api/v1/auth/logout', {
        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer '+window.localStorage.getItem('token')
        }
    });

    const result = await response.json();
    if (result.status == 'Berhasil') {
        window.localStorage.removeItem('token')
        window.location.href = '/';
    } else {
        alert('Logout Gagal')
    }
})
