// create pagination, default show 5 rows
const rows = document.getElementById("rows");
const prev = document.getElementById("prev");
const next = document.getElementById("next");
const pageRow = document.getElementById("page-row");
const table = document.getElementById("table");
const tableBody = document.getElementById("table-body");
const tableRows = document.querySelectorAll("#table-body tr");

let currentPage = 1;
let rowsPerPage = 10;

// show rows
const showRows = (page, rows) => {
  const start = (page - 1) * rows;
  const end = start + rows;
  const paginatedItems = [...tableRows].slice(start, end);

  tableBody.innerHTML = "";
  paginatedItems.forEach((item) => tableBody.appendChild(item));
};

// show page row
const showPageRow = (page, rows) => {
  const start = (page - 1) * rows + 1;
  const end = start + rows - 1;
  const totalRows = tableRows.length;

  pageRow.innerHTML = `${start} - ${end} of ${totalRows}`;
};

// prev page
const prevPage = () => {
  if (currentPage > 1) {
    currentPage--;
    showRows(currentPage, rowsPerPage);
    showPageRow(currentPage, rowsPerPage);
  }
};

// next page
const nextPage = () => {
  if (currentPage < Math.ceil(tableRows.length / rowsPerPage)) {
    currentPage++;
    showRows(currentPage, rowsPerPage);
    showPageRow(currentPage, rowsPerPage);
  }
};

// on change rows per page option
rows.addEventListener("change", (e) => {
  rowsPerPage = parseInt(e.target.value);
  currentPage = 1;
  showRows(currentPage, rowsPerPage);
  showPageRow(currentPage, rowsPerPage);
});

// on click prev button
prev.addEventListener("click", prevPage);

// on click next button
next.addEventListener("click", nextPage);

// show first page
showRows(currentPage, rowsPerPage);
showPageRow(currentPage, rowsPerPage);
