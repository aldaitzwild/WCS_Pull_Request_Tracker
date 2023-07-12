document.addEventListener('DOMContentLoaded', () => {
    const starElements = document.querySelectorAll('[data-toggle-follow-target="star"]');
    starElements.forEach(starElement => {
        starElement.addEventListener('click', toggle);
    });
});

const saveButton = document.getElementById('saveButton');
if (saveButton) {
    saveButton.addEventListener('click', () => {
        location.reload();
    });
}

function toggle(event) {
    event.preventDefault();

    const url = event.target.dataset.url;
    const starElement = event.target;
    const projectContainer = starElement.closest('.project-container');
    let projectName = '';

    if (projectContainer) {
        const projectNameElement = projectContainer.querySelector('.project-name');
        if (projectNameElement) {
            projectName = projectNameElement.textContent;
        }
    }

    fetch(url, { method: 'GET' })
        .then(response => response.json())
        .then(data => {
            starElement.outerHTML = data.isFollowed ? '<i class="bi bi-star" data-toggle-follow-target="star"></i>' : '<i class="bi bi-star-fill" data-toggle-follow-target="star"></i>';
            if (projectContainer) {
                projectContainer.style.display = data.isFollowed ? 'block' : 'none';
            }

            if (!data.isFollowed) {
                const hiddenProjectsContainer = document.getElementById('hidden-projects-container');

                const newHiddenProject = document.createElement('div');
                newHiddenProject.setAttribute('data-project-id', data.id);

                var starClass = 'bi bi-star';
                if (data.isFollowed) {
                    starClass = 'bi bi-star-fill';
                }
                const projectHTML = `<p class="font-link">${projectName}<a><i class="${starClass} text-warning font-link" data-toggle-follow-target="star" data-url="${url}"></i></a></p>`;

                newHiddenProject.innerHTML = projectHTML;
                hiddenProjectsContainer.appendChild(newHiddenProject);

                newHiddenProject.querySelector('[data-toggle-follow-target="star"]').addEventListener('click', toggle);
            }
        })
        .catch(error => console.error('Error:', error));
}