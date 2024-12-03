document.addEventListener('DOMContentLoaded', function () {
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    const dashboard = document.getElementById('dashboard');
    const results = document.getElementById('results');
    const searchBox = document.getElementById('search-box');
    const searchBtn = document.getElementById('search-btn');
    const backBtn = document.getElementById('back-btn');
    const errorMessage = document.getElementById('error-message');
    const userDetailsModal = document.getElementById('userDetailsModal');

    // Display search results if there's any search term passed from PHP
    <?php if (!empty($search_term)): ?>
        if (dashboard && results && backBtn && searchBox) {
            dashboard.style.display = 'none';
            results.style.display = 'block';
            backBtn.style.display = 'block';
            searchBox.value = '<?php echo htmlspecialchars($search_term); ?>';
        }
    <?php endif; ?>

    document.addEventListener('DOMContentLoaded', function () {
        const searchBtn = document.getElementById('search-btn');
        const searchBox = document.getElementById('search-box');
        
        // Perform search functionality
        function performSearch() {
            const searchTerm = searchBox.value.trim();
            if (searchTerm) {
                // For now, just show an alert or handle it however you want
                alert("Searching for: " + searchTerm);
                window.location.href = `admin.php?search_term=${encodeURIComponent(searchTerm)}`;
            } else {
                alert("Please enter a search term.");
            }
        }
    
        // Attach event listener to the search button
        if (searchBtn) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();  // Prevent the form from submitting traditionally
                performSearch();  // Trigger search via JavaScript
            });
        }
    });
    

    // Attach event listener to the search button
    if (searchBtn) {
        searchBtn.addEventListener('click', performSearch);
    }

    // Back button functionality
    if (backBtn) {
        backBtn.addEventListener('click', () => {
            if (dashboard && results && backBtn && searchBox) {
                dashboard.style.display = 'grid';
                results.style.display = 'none';
                backBtn.style.display = 'none';
                searchBox.value = ''; // Clear the search box
            }
        });
    }

    // Function to display user details in modal
    function showUserDetails(user) {
        if (userDetailsModal) {
            document.getElementById('userFullName').innerText = user.first_name + ' ' + user.middle_name + ' ' + user.last_name;
            document.getElementById('userEmail').innerText = user.email;
            document.getElementById('userFirstName').innerText = user.first_name;
            document.getElementById('userMiddleName').innerText = user.middle_name;
            document.getElementById('userLastName').innerText = user.last_name;
            document.getElementById('userAddress').innerText = user.address;
            
            userDetailsModal.classList.remove('hidden');  // Show the modal
        }
    }

    // Close the modal
    function closeModal() {
        if (userDetailsModal) {
            userDetailsModal.classList.add('hidden');
        }
    }

    // Handle any error messages (optional)
    if (errorMessage) {
        // Logic to handle error messages
    }
});
