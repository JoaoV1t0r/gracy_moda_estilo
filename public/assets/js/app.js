// ================================================================================
function adicionarCarrinho(id_produto) {
    axios.default.withCredentials = true;
    axios.get('/adiconar_carrinho?id_produto=' + id_produto)
        .then(function(response){
            let total_produtos = response.data
            document.getElementById('carrinho').innerText = total_produtos;
    })
}