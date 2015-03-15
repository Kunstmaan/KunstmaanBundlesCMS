define(function () {
  // Indonesian
  return {
    inputTooLong: function (args) {
      var overChars = args.input.length - args.maximum;

      return 'Hapuskan ' + overChars + ' huruf';
    },
    inputTooShort: function (args) {
      var remainingChars = args.minimum - args.input.length;

      return 'Masukkan ' + remainingChars + ' huruf lagi';
    },
    loadingMore: function () {
      return 'Mengambil data…';
    },
    maximumSelection: function (args) {
      return 'Anda hanya dapat memilih ' + args.maximum + ' pilihan';
    },
    noResults: function () {
      return 'Tidak ada data yang sesuai';
    },
    searching: function () {
      return 'Mencari…';
    }
  };
});
