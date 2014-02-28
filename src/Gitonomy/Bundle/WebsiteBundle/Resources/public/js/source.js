$(document).ready(function() {
  $('td.source > pre').each(function(i, e) {hljs.highlightBlock(e)});
});
