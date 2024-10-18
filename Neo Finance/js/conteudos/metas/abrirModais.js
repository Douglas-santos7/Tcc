
    // Função para abrir o modal de adicionar meta
    function abrirModalAdicionar() {
        document.getElementById('pop-up-adicionar-container').style.display = 'block';
    }

    // Fechar o modal de adicionar
    var closeBtnAdicionar = document.getElementById('btn-fechar-popup-adicionar');
    closeBtnAdicionar.onclick = function () {
        document.getElementById('pop-up-adicionar-container').style.display = 'none';
    }

    // Função para abrir o modal de depositar
    function abrirModalDepositar(idMeta) {
        document.getElementById('id_meta_depositar').value = idMeta;
        document.getElementById('pop-up-depositar-container').style.display = 'block';
    }

    // Fechar o modal de depositar
    var closeBtnDepositar = document.getElementById('btn-fechar-popup-depositar');
    closeBtnDepositar.onclick = function () {
        document.getElementById('pop-up-depositar-container').style.display = 'none';
    }

    // Função para abrir o modal de resgatar
    function abrirModalResgatar(idMeta) {
        document.getElementById('id_meta_resgatar').value = idMeta;
        document.getElementById('pop-up-resgatar-container').style.display = 'block';
    }

    // Fechar o modal de resgatar
    var closeBtnResgatar = document.getElementById('btn-fechar-popup-resgatar');
    closeBtnResgatar.onclick = function () {
        document.getElementById('pop-up-resgatar-container').style.display = 'none';
    }

    // Função para abrir o modal do histórico
    function abrirModalHistorico(idMeta) {
        var modalHistorico = document.getElementById('pop-up-historico-container');
        var historicoConteudo = document.getElementById('historico-conteudo');

        modalHistorico.style.display = 'block';
    }

    // Fechar o modal do histórico
    var closeBtnHistorico = document.getElementById('btn-fechar-popup-historico');
    closeBtnHistorico.onclick = function () {
        document.getElementById('pop-up-historico-container').style.display = 'none';
    }

    // Fechar o modal se clicar fora dele
    window.onclick = function (event) {
        var modalAdicionar = document.getElementById('pop-up-adicionar-container');
        var modalDepositar = document.getElementById('pop-up-depositar-container');
        var modalResgatar = document.getElementById('pop-up-resgatar-container');
        var modalHistorico = document.getElementById('pop-up-historico-container');
        
        if (event.target == modalAdicionar) {
            modalAdicionar.style.display = 'none';
        } else if (event.target == modalDepositar) {
            modalDepositar.style.display = 'none';
        } else if (event.target == modalResgatar) {
            modalResgatar.style.display = 'none';
        } else if (event.target == modalHistorico) {
            modalHistorico.style.display = 'none';
        }
    }
