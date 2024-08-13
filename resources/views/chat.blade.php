@extends('chat.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-semibold mb-6">User List</h2>
                    <div id="user-list" class="space-y-4">
                        <!-- Data pengguna akan dimasukkan di sini oleh JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        async function fetchUsers() {
            try {
                const response = await fetch('/api/v1/user/all', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + window.localStorage.getItem('token')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                return result.users;
            } catch (error) {
                console.error('Error fetching users from API:', error);
                return null;
            }
        }

        async function displayUsers() {
            const users = await fetchUsers();
            const userList = document.getElementById('user-list');

            if (users && users.length > 0) {
                users.forEach(user => {
                    const userElement = document.createElement('div');
                    userElement.classList.add('p-4', 'bg-gray-100', 'hover:bg-gray-200', 'rounded-lg', 'shadow-sm', 'transition-all', 'duration-300');

                    const userLink = document.createElement('a');
                    userLink.href = `{{ url('/chat_ui') }}/${user.id}`;
                    userLink.textContent = user.name;
                    userLink.classList.add('text-blue-500', 'hover:text-blue-700', 'font-medium');

                    userElement.appendChild(userLink);
                    userList.appendChild(userElement);
                });
            } else {
                userList.textContent = 'No users found.';
            }
        }

        displayUsers(); // Memanggil fungsi untuk menampilkan data pengguna
    </script>
@endsection
