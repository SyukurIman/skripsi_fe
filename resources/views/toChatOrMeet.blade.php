@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-md">
        <div class="flex justify-between mb-4">
            <div>
                    <button type="button" id="fetchDataBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Meet
                    </button>
                    <div id="result"></div>
            </div>
            <div>
                <a class="text-blue-500 text-lg font-semibold flex items-center mb-2" href="{{ route('chat_dashboard') }}">
                    <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Chat
                    </button>
                </a>
            </div>
        </div>
        @if (session('error'))
            <div class="bg-red-500 text-white p-3 rounded-md mt-4">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Ambil elemen tombol dan div hasil
    const fetchDataBtn = document.getElementById('fetchDataBtn');
    const resultDiv = document.getElementById('result');

    // Tambahkan event listener pada tombol
    fetchDataBtn.addEventListener('click', async function() {
        const url = 'https://sejiwa.cakuide.com/api/v1/meeting';
        const url_2 = 'https://sejiwa.cakuide.com/meeting/lakh-drznt';
        const token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE3MjMzNDIxMDAsImV4cCI6MTcyMzM2MDEwMCwibmJmIjoxNzIzMzQyMTAwLCJqdGkiOiIyWnJFNlpScUsyZlZkTENKIiwic3ViIjoiMDkyNjg4NDgtYjU2MS00ZDc5LWE5NmYtNzU2YmU2MjZhYjI3IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.2micE2AODADZ9WmboZVJJRrLl1GSy9URHhJr_2_Ycm0'
        fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            console.log(response.body)
            if (response.ok) {
                console.log(response.json())
                window.location.href = url_2;
            } else {
                alert('Failed to authenticate. Status: ' + response.status);
            }
        })
        .catch(error => {
            console.error('Error during fetch:', error);
        });

    });
</script>

<script>

</script>
@endsection
