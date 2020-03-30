// Passos para a produção do ECMA-262, Edition 5, 15.4.4.14
// Referência: http://es5.github.io/#x15.4.4.14
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function(elementoDePesquisa, pontoInicial) {

    var k;

    //1. Deixe-o ser o resultado da chamada de toObject
    // passando o valor de this como argumento.
    if (this == null) {
      throw new TypeError('"this" é nulo (null) ou não foi definido (undefined');
    }

    var O = Object(this);

    // 2. Deixar o tamanhoValor ser o resultado da
    // chamada do método interno Get de 0 com o
    // argumento "length"
    // 3. Deixar o  tamanhoValor ser um ToUint32(tamanhoValor).
    var tamanho = O.length >>> 0;

    // 4. se o tamanho é 0, retorna -1.
    if (tamanho === 0) {
      return -1;
    }

    // 5. Se o argumento pontoInicial for passado, use o ToInteger(pontoInicial); senao use 0.
    var n = + pontoInicial || 0;

    if (Math.abs(n) === Infinity) {
      n = 0;
    }

    //6. Se n >= tamanho, retorna -1.
    if (n >= tamanho) {
      return -1;
    }

    // 7. Se n>= 0, entao k seja n.
    // 8. Senao, n<0, k seja tamanho - abs(n).
    // Se k é menor que 0, entao k seja 0.
    k = Math.max(n >= 0 ? n : tamanho - Math.abs(n), 0);

    // 9. Repita, enquanto k < tamanho
    while (k < tamanho) {
      // a. Deixe Pk ser ToString(k).
      //    isto é implicito para operandos LHS de um operador

      // b. Deixe o kPresent  ser o resultado da chamada do método interno de 0 com argumento Pk
      //      Este passo pode ser combinado com c.
      // c. Se kPresent é true, entao
      //    i.  Deixe o  elementK ser o resultado da chamada do metodo interno Get de 0 com argumento ToString(k)
      //   ii.  Deixe o resultado ser aplicado pelo Algoritmo de
      //        Comparação de Igualdade Estrita (Strict Equality Comparison) para o elementoDePesquisa e elementK
      //  iii.  caso verdadeiro, retorne k.
      if (k in O && O[k] === elementoDePesquisa) {
        return k;
      }
      k++;
    }
    return -1;
  };
}



// Passos para a produção do ECMA-262, Edition 5, 15.4.4.14
// Referência: http://es5.github.io/#x15.4.4.14
if (!Array.prototype.indexOfOb) {
  Array.prototype.indexOfOb = function(Campo, elementoDePesquisa) {

    var k;

    //1. Deixe-o ser o resultado da chamada de toObject
    // passando o valor de this como argumento.
    if (this == null) {
      throw new TypeError('"this" é nulo (null) ou não foi definido (undefined');
    }

    var O = Object(this);

    // 2. Deixar o tamanhoValor ser o resultado da
    // chamada do método interno Get de 0 com o
    // argumento "length"
    // 3. Deixar o  tamanhoValor ser um ToUint32(tamanhoValor).
    var tamanho = O.length >>> 0;

    // 4. se o tamanho é 0, retorna -1.
    if (tamanho === 0) {
      return -1;
    }

    // 5. Se o argumento pontoInicial for passado, use o ToInteger(pontoInicial); senao use 0.
    var n = 0;

    


    // 7. Se n>= 0, entao k seja n.
    // 8. Senao, n<0, k seja tamanho - abs(n).
    // Se k é menor que 0, entao k seja 0.
    k = Math.max(n >= 0 ? n : tamanho - Math.abs(n), 0);

    // 9. Repita, enquanto k < tamanho
    while (k < tamanho) {
      // a. Deixe Pk ser ToString(k).
      //    isto é implicito para operandos LHS de um operador

      // b. Deixe o kPresent  ser o resultado da chamada do método interno de 0 com argumento Pk
      //      Este passo pode ser combinado com c.
      // c. Se kPresent é true, entao
      //    i.  Deixe o  elementK ser o resultado da chamada do metodo interno Get de 0 com argumento ToString(k)
      //   ii.  Deixe o resultado ser aplicado pelo Algoritmo de
      //        Comparação de Igualdade Estrita (Strict Equality Comparison) para o elementoDePesquisa e elementK
      //  iii.  caso verdadeiro, retorne k.
      if (k in O && O[k][Campo] === elementoDePesquisa) {
        return k;
      }
      k++;
    }
    return -1;
  };
}