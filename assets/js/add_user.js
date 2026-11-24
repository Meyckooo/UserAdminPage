function generateRandomPostCode() {
  const randomCode = Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, "0");
  document.getElementById("post_code").value = randomCode;
}

document.getElementById("addUserForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const form = this;
  const postCode = document.getElementById("post_code").value;

  // 1. Check kon duplicate ba ang Post Code
  fetch("../check_postcode.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "post_code=" + encodeURIComponent(postCode),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "exists") {
        Swal.fire({
          title: "Post Code Already Exists!",
          text:
            'The post code "' +
            postCode +
            '" is already in use. Please generate another code.',
          icon: "warning",
          confirmButtonText: "OK",
        });
      } else if (data.status === "available") {
        if (!form.querySelector('input[name="add"]')) {
          const addInput = document.createElement("input");
          addInput.type = "hidden";
          addInput.name = "add";
          addInput.value = "1";
          form.appendChild(addInput);
        }
        form.submit();
      } else {
        Swal.fire({
          title: "Error!",
          text:
            data.message || "Could not verify the post code. Please try again.",
          icon: "error",
          confirmButtonText: "OK",
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        title: "Error!",
        text: "Could not verify the post code. Please try again.",
        icon: "error",
        confirmButtonText: "OK",
      });
    });
});
