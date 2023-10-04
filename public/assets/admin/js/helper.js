// Setting fontsise
function setFontsize(target, event) {
  const font = $(event.target).attr('data-font');
  const dropdown = $('.dropdown-item');
  const targetArray = target.split(',');
  $.each(dropdown, function (index, value) { 
    $(value).removeClass('active');
  });
  // $(target).css('font-size', font+'em');
  console.log(targetArray)
  if(targetArray.length > 0) {
    $.each(targetArray, function(index, value) {
      $(value.trim()).css('font-size', font+'em');
    })
  }else {
    $(target).css('font-size', font+'em');
  }
  
  $(event.target).addClass('active');
}

// Format Rupiah
function formatRupiah(angka, prefix){
  // console.log(angka.replace())
  var number_string = angka.toString().replace(/[^,\d]/g, ''),
  split   		= number_string.split(','),
  sisa     		= split[0].length % 3,
  rupiah     		= split[0].substr(0, sisa),
  ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if(ribuan){
      separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
  return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

// Play spinner
function spinner(targetButton, stop = false) {
  if(!stop) {
    $(targetButton).html(`<div class="spinner-border spinner-border-sm me-2 text-light" role="status">
                            <span class="sr-only">Loading...</span>
                          </div> Loading`);
  }else {
    $(targetButton).html(`Simpan`);
  }
}

function spinnerNew(object = {target: '', stop: false, textLoad: 'Loading...', textStop: 'Simpan'}) {
  if(!object.stop) {
    $(object.target).html(`<div class="spinner-border spinner-border-sm me-2 text-light" role="status">
                            <span class="sr-only">${object.textLoad}</span>
                          </div> Loading`);
  }else {
    $(object.target).html(object.textStop);
  }
}