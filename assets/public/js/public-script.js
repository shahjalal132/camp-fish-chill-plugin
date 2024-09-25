(function ($) {
  $(document).ready(function () {

    // Handle pagination click
    $(document).on("click", "#camp-pagination a", function (e) {
      e.preventDefault();

      // Extract the page number from the pagination link's href
      var href = $(this).attr("href");
      var page = href.includes("page/") ? href.split("page/")[1] : 1;

      ajaxLoadPosts(page);
    });

    // AJAX function to load posts based on the current page
    function ajaxLoadPosts(page) {
      $.ajax({
        url: ajaxpagination.ajaxurl,
        type: "POST",
        data: {
          action: "load_posts",
          page: page,
          nonce: ajaxpagination.nonce,
        },
        success: function (response) {
          // Update the posts content and pagination
          $("#posts-loop").html(response.posts);
          $("#camp-pagination").html(response.pagination);
        },
        error: function (error) {
          console.log("Error loading posts:", error);
        },
      });
    }
  });
})(jQuery);
