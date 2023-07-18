function checkIsConnected() {
  return fetch('/check_auth')
    .then(response => response.json())
    .then(data => data.isConnected);
}
function refreshPullRequests() {
  checkIsConnected().then(isConnected => {
    if (isConnected) {
      fetch('/pullrequest/api')
        .then(response => response.text())
        .then(data => {
          document.getElementById('pullRequestsComponent').innerHTML = data;
        });
    }
  });
}

setInterval(refreshPullRequests, 10000);