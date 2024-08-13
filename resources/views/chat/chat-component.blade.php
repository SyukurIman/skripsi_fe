@extends('layouts.app')

@section('content')
<div class="container_message">
    <div class="chat-section">
        <div class="chat-header" id="chat-header">

        </div>
        <div class="chat-messages" id="chat-box">
        </div>
        <form id="chat-form">
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let reciver = '';
    let numberKey ='';

    async function getLink() {
        try {
            const response = await fetch(`/api/v1/chat/linkkey/{{$id}}`, {
                method:'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + window.localStorage.getItem('token')
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log(result.linkKey.sesi.waktu_mulai);
            if(result.linkKey.dokter.user.id != result.sender.id){
                reciver = result.linkKey.dokter.user.id;
            }else if (result.linkKey.user.id != result.sender.id){
                reciver = result.linkKey.user.id;
            }
            const chatBox = document.getElementById('chat-header');
            if(result.linkKey.dokter.user.id != result.sender.id){
                chatBox.innerHTML = `<h2> Dokter ${result.linkKey.dokter.user.name}</h2>`;
            }else if (result.linkKey.user.id != result.sender.id){
                chatBox.innerHTML = `<h2>${result.linkKey.user.name}</h2>`;
            }
            numberKey = result.linkKey.number_key;
            console.log('reciver', reciver)
            fetchDataChat(result.linkKey.number_key);
        }catch (error) {
            console.error('Error fetching chat data from API:', error);
            return null;
        }
    }
    getLink();

    async function fetchDataChat(key) {
        try {
            const response = await fetch('/api/v1/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + window.localStorage.getItem('token')
                },
                body: JSON.stringify({ number_key: key })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log(result.status);

            if(result.status == 'Gagal'){
                cekStatus(result.pesan)
            } else if(result.status == 'Berhasil'){
                const formMassage = document.getElementById('chat-form');
                // Periksa apakah form sudah ada, jika belum, baru tambahkan
                if (!formMassage.querySelector('.chat-input')) {
                    formMassage.innerHTML = `<div class="chat-input">
                        <input name="message" id="message" type="text" placeholder="Ketik pesan...">
                        <button type="submit" id="sendButton">Kirim</button>
                    </div>`;
                }
            }

            displayMessages(result.messages);
            // valueForm(result);
            return result;
        } catch (error) {
            console.error('Error fetching chat data from API:', error);
            return null;
        }
    }

    function cekStatus(pesan){
        const disableForm = document.getElementById('chat-form');
        disableForm.innerHTML =`<div class="error">
                    <div class="error__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" height="24" fill="none"><path fill="#393a37" d="m13 13h-2v-6h2zm0 4h-2v-2h2zm-1-15c-1.3132 0-2.61358.25866-3.82683.7612-1.21326.50255-2.31565 1.23915-3.24424 2.16773-1.87536 1.87537-2.92893 4.41891-2.92893 7.07107 0 2.6522 1.05357 5.1957 2.92893 7.0711.92859.9286 2.03098 1.6651 3.24424 2.1677 1.21325.5025 2.51363.7612 3.82683.7612 2.6522 0 5.1957-1.0536 7.0711-2.9289 1.8753-1.8754 2.9289-4.4189 2.9289-7.0711 0-1.3132-.2587-2.61358-.7612-3.82683-.5026-1.21326-1.2391-2.31565-2.1677-3.24424-.9286-.92858-2.031-1.66518-3.2443-2.16773-1.2132-.50254-2.5136-.7612-3.8268-.7612z"></path></svg>
                    </div>
                    <div class="error__title">${pesan}</div>
                </div>`;
        clearInterval(intervalId);
    }
    // cekStatus();
    function displayMessages(messages) {
    const chatBox = document.getElementById('chat-box');
    chatBox.innerHTML = ''; // Clear chat box before appending new messages

    messages.forEach(message => {
        let chat = '';

        if (message.receiver_id == reciver) { // Pastikan 'reciver' didefinisikan
            chat = `
                <div class="message sent">
                    <p>${message.message}</p>
                    <span class="timestamp">${message.waktu}</span>
                </div>`;
        } else {
            chat = `
                <div class="message received">
                    <p>${message.message}</p>
                    <span class="timestamp">${message.waktu}</span>
                </div>`;
        }

        // Menambahkan pesan ke dalam chatBox
        chatBox.insertAdjacentHTML('beforeend', chat);
    });
    }

    // Fungsi untuk memeriksa pesan baru
    async function checkNewMessages() {
        try {
            const result = await fetchDataChat(numberKey);
            console.log(result);
            displayMessages(result.messages);
        } catch (error) {
            console.error('Error checking for new messages:', error);
        }
    }

    // Jalankan fungsi checkNewMessages() setiap 5 detik
    const intervalId = setInterval(checkNewMessages, 5000);

    document.getElementById('chat-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const messageData = {
            receiver_id: reciver,
            message: document.getElementById('message').value,
            number_key: numberKey
        };
        console.log(messageData);
        fetch('/api/v1/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window.localStorage.getItem('token')
            },
            body: JSON.stringify(messageData)
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.data);
            const chatBox = document.getElementById('chat-box');
            let chat =`
                <div class="message sent">
                    <p>${data.data.message}</p>
                    <span class="timestamp">${data.data.waktu}</span>
                </div>`;

            chatBox.insertAdjacentHTML('beforeend', chat);

            document.getElementById('message').value = ''; // Clear the message input
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });
    });
</script>
<style>
.container_message {
    display: flex;
    width: 100%;
    height: 100vh;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}
.chat-section {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.chat-header {
    background-color: #007bff;
    color: #fff;
    padding: 15px;
    text-align: center;
}

.chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
}

.message {
    max-width: 70%;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 15px;
    position: relative;
    font-size: 14px;
}

.message p {
    margin: 0;
}

.message .timestamp {
    font-size: 12px;
    color: #aaa;
    position: absolute;
    bottom: -18px;
    right: 10px;
}
.message.sent {
    max-width: 50%;
    background-color: #007bff;
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 0;
    word-wrap: break-word;
    margin-bottom: 20px;
}

.message.received {
    max-width: 50%;
    background-color: #f1f0f0;
    color: #333;
    border-bottom-left-radius: 0;
    word-wrap: break-word;
    margin-bottom: 20px;
}

.chat-input {
    display: flex;
    border-top: 1px solid #ddd;
}

.chat-input input {
    flex: 1;
    padding: 15px;
    border: none;
    outline: none;
}

.chat-input button {
    padding: 0 20px;
    background-color: #007bff;
    border: none;
    color: #fff;
    cursor: pointer;
}

.chat-input button:hover {
    background-color: #0056b3;
}

.error {
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  width: auto;
  padding: 12px;
  display: flex;
  margin-bottom: 20px;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  background: #EF665B;
  border-radius: 8px;
  box-shadow: 0px 0px 5px -3px #111;
}

.error__icon {
  width: 20px;
  height: 20px;
  transform: translateY(-2px);
  margin-right: 8px;
}

.error__icon path {
  fill: #fff;
}

.error__title {
  font-weight: 500;
  font-size: 14px;
  color: #fff;
}

.error__close {
  width: 20px;
  height: 20px;
  cursor: pointer;
  margin-left: auto;
}

.error__close path {
  fill: #fff;
}

@media (max-width: 768px) {
    .container {
        flex-direction: column;
        height: 100%;
    }

    .chat-section {
        width: 100%;
    }
    .message.sent {
        max-width: 100%;
    }
    .message.received {
        max-width: 100%;
    }

}

@media (max-width: 480px) {
    .container {
        height: 100%;
    }
    .message {
        max-width: 100%;
    }

    .chat-input input {
        padding: 10px;
    }

    .chat-input button {
        padding: 0 10px;
    }
    .message.sent {
        max-width: 100%;
    }
    .message.received {
        max-width: 100%;
    }
}


</style>

@endsection
