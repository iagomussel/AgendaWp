/*
*
*  Gerador Aleatorio de Palavras 
*	--Iago Mussel--
*
*/
gerador = {
    vogal: [ "a", "e", "i", "o", "u" ],
    consoante: [ "b", "c", "d", "f", "g", "h", "j", "m", "n", "p", "q", "s", "t", "v", "x", "z" ],
    lr: [ "r", "l" ],
    lrh_meio: [ "r", "l", "lh", "ch", "nh" ],
    lrs: [ "r", "l", "s" ],
palavra : function() {
    q_sil = 2 + parseInt(100 * Math.random()) % 3;
    x = 0;
    palavra = "";
    while (x < q_sil) {
        palavra += gerador.si();
        x++;
    }
    return palavra;
},
si : function() {
    var a = "";
    if (parseInt(100 * Math.random()) % 2) a += gerador.vogal[parseInt(100 * Math.random()) % gerador.vogal.length]; else if (parseInt(100 * Math.random()) % 2) {
        a += gerador.lrh_meio[parseInt(100 * Math.random()) % gerador.lrh_meio.length];
        a += gerador.vogal[parseInt(100 * Math.random()) % gerador.vogal.length];
    } else {
        a += gerador.consoante[parseInt(100 * Math.random()) % gerador.vogal.length];
        if (parseInt(100 * Math.random()) % 2) a += gerador.lr[parseInt(100 * Math.random()) % gerador.lr.length];
        a += gerador.vogal[parseInt(100 * Math.random()) % gerador.vogal.length];
    }
    if (parseInt(100 * Math.random()) % 2) a += gerador.lrs[parseInt(100 * Math.random()) % gerador.lrs.length];
    return a;
}

};