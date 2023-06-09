const searchForm = document.getElementById('search-form');
const searchResult = document.getElementById('search-result');
const url = searchForm.dataset.searchContributorUrl;
const projectId = parseInt(searchForm.dataset.projectId);

searchForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const formData = new FormData(searchForm);
    const response = await fetch(url, {
        method: 'POST',
        body: formData,
    });

    searchResult.innerHTML = '';

    if (response.ok) {
        const result = await response.json();
        updateResult(result);
    } else {
        const message = `<p class="found-text">Contributor not found</p>`;
        searchResult.insertAdjacentHTML('beforeend', message);
    }
});

function updateResult(result) {
    if (result.success) {
        const {githubName, id: contributorId} = result.result;
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
    }
}
