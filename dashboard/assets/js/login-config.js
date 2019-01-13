$(function(){
    $("[data-hide]").on("click", function(){
        $("." + $(this).attr("data-hide")).hide();
        // -or-, unique alert
        // $(this).closest("." + $(this).attr("data-hide")).hide();
    });
});