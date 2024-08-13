<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Video Call</title>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f0f4f8;
                margin: 0;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }

            .container {
                width: 100%;
                max-width: 1280px;
                padding: 1rem;
                margin: auto;
            }

            .video-container, .control-container, .form-container, .meeting-container, .waiting-container {
                flex-direction: column;
                align-items: center;
                background-color: #fff;

            }

            .form-container {
                width: 100%;
                display: flex;
                flex-direction: column;
            }

            .video-container {
                padding: 5px;
                border-radius: 5px;
                box-shadow: 0px 0px 4px rgba(0, 0, 0, 0.1);
                margin-bottom: 1rem;
            }

            .waiting-container, .meeting-container{
                padding: 2rem;
                border-radius: 1rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .video-container video, .meeting-container video {
                width: 100%;
                max-height: 300px;
                border-radius: 1rem;
            }

            .control-container button, .form-container select, .form-container input{
                margin: 0.5rem;
                background-color: #e2e8f0;
                border: none;
                border-radius: 0.5rem;
                padding: 0.5rem 1rem;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .control-container button:hover, .form-container select:hover, .form-container input:hover{
                background-color: #cbd5e0;
            }


            .control-container button {
                width: auto;
                height: auto;
            }

            .form-container label, .form-container input {
                width: 100%;
                display: flex;
                flex-direction: column;
            }


            .bootom_meet {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 5%;
                margin-top: 20px;
            }

            #remoteParticipantContainer{
                flex-direction: column;
                row-gap: 15px;
            }

            .participant_c {
                width: 100%;
                height: fit-content;
                background-repeat: no-repeat;
                background-size: contain;
                background-position: center;
            }

            .button_toggle{
                display: flex;
                gap: 5%;
                align-items: center;
            }

            .button_toggle select {
                width: 100%;
                background-color: transparent;
            }

            .control-container{
                display: flex;
            }

            #joinMeetingBtn{
                margin: 1rem;
                padding: 1rem;
                width: 100%;
                flex-direction: column;
            }

            #classificationResult{
                background-color: rgb(249, 251, 255);
                color: black;
                padding: 10px;
                justify-content: center;
                border-radius: 5px;
                box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
                margin-top: 20px;
            }

            #waitingAreaLocalVideo {
                background-color: #e2e8f0;
                border-radius: 5px;
                box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
            }

            #waitingAreaLocalVideo video {
                width: 100%;
            }

            .video-container video, .participant_c video {
                height: fit-content;
                max-height: 216px
            }

            @media (min-width: 300px) {
                .container {
                    padding: 10px;
                }

                .bootom_meet{
                    flex-direction: column;
                }

                #localParticiapntContainer video{
                    height: fit-content;
                }

                .meeting-container {
                    padding: 15px
                }

                .meeting-controls{
                    margin-top: 5px;
                }

                .pb-4 {
                    padding-bottom: 5px;
                }

                .text_video {
                    height: auto;
                    padding: 5px;
                    font-size: smaller;
                }

                .control-container svg, .meeting-controls svg {
                    width: 20px;
                    height: 20px;
                }

                #remoteParticipantContainer, .button_view{
                    display: flex;
                    flex-direction: row;
                    gap: 15%;
                    justify-content: space-evenly;
                    margin-top: 1rem;
                    height: fit-content;
                }

                .button_view {
                    width: 50%
                }

                #t_room_name {
                    font-size: medium;
                }

                #btn_logout {
                    font-size: smaller;
                    align-items: center;
                }

                .text-title-i {
                    align-items: center;
                    display: flex;
                    font-size: medium;
                    font-weight: 600;
                }

                #localParticiapntContainer, #waitingAreaLocalVideo{
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center;
                }

            }

            @media (min-width: 768px) {
                #localParticiapntContainer video{
                    height: 150px;
                }
                #btn_logout {
                    font-size: medium;
                }

                .text-title-i {
                    font-size: x-large;
                }
                #t_room_name {
                    font-size: larger;
                }

                .video-container, .control-container, .meeting-container, .bootom_meet {
                    flex-direction: row;
                }

                .video-container video, .participant_c video {
                    height: fit-content;
                    max-height: 330px;
                }

                .participant_c {
                    height: fit-content;
                    max-height: 330px;
                }

                #remoteParticipantContainer{
                    flex-direction: row;
                    row-gap: 0px;
                    align-items: center;
                }

                .control-container button, .form-container select, .form-container input, .meeting-controls button {
                    margin: 1rem;
                    padding: 1rem 1.5rem;
                }

                .form-container {
                    align-items: center;
                }

                .form-container label {
                    margin: 0 1rem;
                }

                .waiting-container, .meeting-container{
                    padding: 2rem;
                }

                .text_video {
                    font-size: large;
                }

                .text_local {
                    font-size: small;
                }

                .meeting-controls {
                    margin-top: 2rem;
                }

                .pb-4 {
                    padding-bottom: 1rem;
                }

                .control-container svg, .meeting-controls svg {
                    width: 24px;
                    height: 24px;
                }

                #remoteParticipantContainer, .button_view{
                    gap: 1%;
                    justify-content: center;
                }

                .button_view{
                    width: auto;
                }
            }

            @media (min-width: 1024px) {
                .container {
                    padding: 3rem;
                }

                .meeting-container video, .participant_c {
                    max-height: 500px;
                }

                .video-container video{
                    max-height: 388px;
                }

                .control-container button, .form-container select, .form-container input, .meeting-controls button {
                    margin: 1rem;
                }

                .control-container button {
                    width: 50%;
                    height: auto;
                }
            }

            @media (min-width: 1920px) {
                .container {
                    padding: 4rem;
                }

                .video-container video, .meeting-container video, .participant_c {
                    max-height: 540px;
                }

            }
        </style>
    </head>

    <body class="antialiased">
        <div class="container">
            <div id="waitingArea" class="waiting-container">
                <div class="py-4 flex" style="justify-content: space-between">
                    <h1 class="text-2xl text-title-i">Meeting Lobby</h1>

                    <button id="btn_logout" class="bg-red-600 text-white rounded-md p-2 flex gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg> Logout
                    </button>
                </div>

                <div class="video-container bg-gray-900">
                    <video id="waitingAreaLocalVideo" autoplay muted></video>

                    <div class="control-container">
                        <button id="waitingAreaToggleMicrophone" class="button_toggle bg-gray-400 w-10 h-10 rounded-md p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <select class="text-xs" id="microphoneSelectBox"></select>
                        </button>

                        <button id="waitingAreaToggleCamera" class="button_toggle bg-gray-400 w-10 h-10 rounded-md p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <select class="text-xs" id="cameraSelectBox"></select>
                        </button>
                    </div>
                </div>



                <div class="form-container">
                    <label>Meeting ID:</label>
                    <input class="text-xs" id="id_key" type="text" disabled placeholder="Metting ID"/>

                    <button id="joinMeetingBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Join Meeting
                    </button>
                </div>
            </div>

            <div id="meetingView" class="hidden meeting-container">
                <div class="pb-4 flex" style="justify-content: space-between">
                    <h1 class="text-xl font-bold text-title-i" id="t_room_name">Room - </h1>

                    <button id="btn_logout" class="bg-red-600 text-white rounded-md p-2 flex gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg> Logout
                    </button>
                </div>
                <hr class="mb-2">
                <div class="meeting-controls">
                    <div id="remoteParticipantContainer" class="flex mt-10">
                        <div id="waiting_participant" class="participant_c rounded-3xl bg-gray-900 relative">
                            <video id="v_waiting_participant" autoplay class="w-full rounded-t-3xl"></video>
                            <div id="u_waiting_participant" class="text_video absolute h-8 w-full bg-gray-700 rounded-b-3xl bottom-0 text-white text-center font-bold pt-1">Menunggu Partisipan...</div>
                        </div>
                    </div>

                    <div id="classificationResult" class="hidden">
                    </div>

                    <div class="bootom_meet flex">
                        <div id="localParticiapntContainer" class="rounded-3xl bg-gray-900 relative">
                            <video id="localVideoTag" autoplay class="w-full rounded-t-3xl"></video>
                            <div id="localUsername" class="text_video text_local absolute h-8 w-full bg-gray-700 rounded-b-3xl bottom-0 text-white text-center font-bold pt-1">Me</div>
                        </div>

                        <div class="button_view">
                            <button id="toggleMicrophone" class="bg-gray-400 rounded-md p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                            </button>

                            <button id="toggleCamera" class="bg-gray-400 rounded-md p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>

                            <button id="leaveMeeting" class="bg-red-400 text-white rounded-md p-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="leaveMeetingView" class="hidden leave-container">
                <h1 class="text-center text-3xl mt-10 font-bold">
                    You have left the meeting
                </h1>
            </div>
        </div>
    </body>

    @php
    $value = session('token');
    @endphp

    @if ($value)
        <p>Session Value: {{ $value }}</p>
    @endif

    <script>
        var number_key = {!! json_encode($data_key) !!};
        var token =  {!! json_encode($token) !!};
        let inputnumber = document.getElementById("id_key");
        inputnumber.value = number_key

        if (token != null){
            window.localStorage.setItem('token', token)
        } else {
            alert('Anda Tidak Punya Akses !!')
        }


        console.log(token)
    </script>

    <script src="{{ asset('js/sdk.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</html>
