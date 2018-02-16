<template></template>
<script>
    import swal from 'sweetalert2';

    export default {
      props: ['message'],
      created() {
        if (this.message) {
          swal({
            type: 'success',
            title: 'Success!',
            text: this.message,
            timer: 3
          })
        }
      }
    }
</script>