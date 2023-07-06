window.onload = function() {
    fetch("{{ path('next_pullRequests_page_exists', {id:project.id}) }}")
        .then(response => response.json())
        .then(data => {
            if (!data.nextPageExists) {
                document.getElementById('getPrButton').disabled = true;
            }
        });
};