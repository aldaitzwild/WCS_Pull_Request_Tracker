const searchForm = document.getElementById('search-form');
const searchResult = document.getElementById('search-result');
const url = searchForm?.getAttribute('data-ajax-url');
const projectId = window.location.pathname.split('/')[2];

searchForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(searchForm);
    const response = await fetch(url, {
        method: 'POST',
        body: formData, projectId
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
        const { githubName, id: contributorId } = result.result;
        const message = `<p class="found-text">${githubName} found!</p>`;
        searchResult.insertAdjacentHTML('beforeend', message);

        const addContributorBtn = document.createElement('button');
        addContributorBtn.type = 'button';
        addContributorBtn.classList.add('btn', 'btn-success');
        addContributorBtn.textContent = 'Add contributor';

        addContributorBtn.addEventListener('click', () => {
            window.location.href = `${projectId}/addContributor/${contributorId}`;
        });

        searchResult.appendChild(addContributorBtn);
    } else {
        const message = `<p>${result.message}</p>`;
        searchResult.insertAdjacentHTML('beforeend', message);
    }
}
