function t(t,n=1e4){let e=null;return{start:function(){e||(e=setInterval(t,n))},stop:function(){e&&(clearInterval(e),e=null)}}}export{t as u};
