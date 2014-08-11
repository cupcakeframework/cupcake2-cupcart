<?php

/**
 * Classe de Carrinho Simples CupCart
 * Utilizável em Orçamentos e Carrinhos de Compra (Semi-commerce)
 * Ajustável a qualquer tipo de item do cupcake
 * Suporta conteúdo misto (de varias tabelas)
 * @author Ricardo Fiorani
 */
class Carrinho {

    private $_carrinho;
    private $site;

    const NOME_CARRINHO_SESSION = 'CupCart';

    public function __construct(Site $site) {
        session_start();
        $this->_carrinho = (array) $_SESSION[self::NOME_CARRINHO_SESSION];
        $this->site = $site;
    }

    public function adicionar($objeto, $qtd) {
        $item = array(
            'tabela' => $objeto->tabela,
            'id' => $objeto->id,
            'tamanho' => $objeto->tamanho,
            'qtd' => $qtd
        );

        $this->_carrinho[$this->gerarKey($objeto)] = $item;
        $this->salvar();
    }

    public function gerarKey($objeto) {
        return md5($objeto->tabela . '_' . $objeto->id . '_' . $objeto->tamanho);
    }

    public function removerPorObjeto($objeto) {
        unset($this->_carrinho[$this->gerarKey($objeto)]);
        $this->salvar();
    }

    public function removerPorId($id) {
        unset($this->_carrinho[$id]);
        $this->salvar();
    }

    public function listar() {
        $retorno = array();
        foreach ($this->_carrinho as $key => $item) {
            $retorno[$key] = $this->site->ver($item['tabela'], $item['id']);
            $retorno[$key]->qtd = $item['qtd'];
            $retorno[$key]->tamanho = $item['tamanho'];
            $retorno[$key]->categoria = $this->site->ver('tbl_categoria_produtos', $retorno[$key]->categoria);
        }
        return $retorno;
    }

    public function qtdObjeto($tabela, $id) {
        $objeto = $this->site->ver($tabela, $id);
        if (array_key_exists($this->gerarKey($objeto), $this->_carrinho)) {
            return $this->_carrinho[$this->gerarKey($objeto)]['qtd'];
        } else {
            return 0;
        }
    }

    public function salvar() {
        return $_SESSION[self::NOME_CARRINHO_SESSION] = $this->_carrinho;
    }

    public function estaVazio() {
        return empty($this->_carrinho);
    }
    
    public function quantidadeDeItensNoCarrinho(){
        return count($this->_carrinho);
    }

}
