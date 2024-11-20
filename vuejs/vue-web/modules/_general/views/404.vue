<template>
	<div class="not-found">
		<div class="container">
			<h1>Whoops!</h1>
			<p class="italic">Uh oh, we can't seem to find the page you're looking for.</p>
			<p class="italic">Try going back to the previous page or <a style="text-decoration:underline;" @click="goBack">Go Back</a>.</p>
		</div>
	</div>
</template>

<script>
export default {
	methods: {
		goBack() {
			this.$router.go(-1); // Vue Router's method to go back
		}
	}
}
</script>

<style scoped>
.not-found {
  font-family: Arial, sans-serif;
  text-align: center;
}

.container {
  margin: 100px auto;
}

h1 {
  font-size: 60px;
  font-weight: bold;
}

.italic {
  font-style: italic;
  margin-bottom: 20px;
}

a {
  cursor: pointer;
  color: #3498db;
}
</style>