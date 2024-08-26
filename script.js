document.getElementById('open-privacy-modal').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('privacy-modal').style.display = 'block';
  });
  
  document.getElementsByClassName('close')[0].addEventListener('click', function() {
    document.getElementById('privacy-modal').style.display = 'none';
  });
  
  window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('privacy-modal')) {
      document.getElementById('privacy-modal').style.display = 'none';
    }
  });
  
  function showSignup() {
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('signup-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Cadastre-se';
    document.querySelector('.form-wrapper').classList.add('signup-mode');
  }
  
  function showLogin() {
    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Login';
    document.querySelector('.form-wrapper').classList.remove('signup-mode');
  }
  
  function login() {
    alert("Logado com sucesso!");
  }
  
  function signup() {
    alert("Cadastro realizado com sucesso!");
    showLogin();
  }
  