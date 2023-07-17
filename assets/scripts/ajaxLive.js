function refreshPullRequests() {
    fetch('/pullrequest/api')
      .then(response => response.text())
      .then(data => {
        document.getElementById('pullRequestsComponent').innerHTML = data;
        console.log('refreshing ..')
      });
  }
  
setInterval(refreshPullRequests, 10000);