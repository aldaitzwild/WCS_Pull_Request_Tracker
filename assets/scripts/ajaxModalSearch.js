const searchForm = document.getElementById('search-form');
const searchResult = document.getElementById('search-result');
const url = searchForm.getAttribute('data-ajax-url');
const projectId = window.location.pathname.split('/')[2];
console.log(projectId);


searchForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    const formData = new FormData(searchForm);
    const response = await fetch(url, {
        method: 'POST',
        body: formData
    });

    if (response.ok) {
        const result = await response.json();
        updateResult(result);
    } else {
        console.error('Erreur lors de la requête AJAX');
    }
});

function updateResult(result) {
    searchResult.innerHTML = '';

    if (result.success) {
        const contributor = result.result;
        const message = `<p><strong>${contributor.githubName}</strong> found!</p>`;
        searchResult.insertAdjacentHTML('beforeend', message);
        const addContributorBtn = document.createElement('button');
        addContributorBtn.type = 'button';
        addContributorBtn.classList.add('btn', 'btn-success');
        addContributorBtn.textContent = 'Add contributor';
        addContributorBtn.addEventListener('click', function () {
            console.log(projectId);
            const contributorId = contributor.id;
            window.location.href = `${projectId}/addContributor/${contributorId}`;
        });
        searchResult.appendChild(addContributorBtn);
    } else {
        const message = `<p>${result.message}</p>`;
        searchResult.insertAdjacentHTML('beforeend', message);
    }
}