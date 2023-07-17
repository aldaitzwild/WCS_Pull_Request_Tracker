function refreshPullRequests() {
    fetch('/pullrequest/api')
      .then(response => response.text())
      .then(data => {
        document.getElementById('pullRequestsComponent').innerHTML = data;
      });
  }

setInterval(refreshPullRequests, 10000);