document.addEventListener('DOMContentLoaded', function() {
    var btnGetPr = document.getElementById('btn-get-pr');
    btnGetPr.addEventListener('click', function() {
        var url = this.getAttribute('data-action');
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    // Mettez à jour votre interface utilisateur avec les données de la réponse JSON
                    // Exemple : document.getElementById('project-name').textContent = response.project.name;
                } else {
                    // Gérez les erreurs éventuelles
                }
            }
        };
        xhr.send();
    });
});
