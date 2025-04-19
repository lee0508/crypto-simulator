<script src="https://accounts.google.com/gsi/client" async defer></script>
<div id="g_id_onload"
  data-client_id="YOUR_CLIENT_ID"
  data-callback="onGoogleSignIn">
</div>
<div class="g_id_signin" data-type="standard"></div>

<script>
  function onGoogleSignIn(response) {
    const userData = jwt_decode(response.credential);
    fetch('google-login.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(userData)
    }).then(() => location.reload());
  }
</script>