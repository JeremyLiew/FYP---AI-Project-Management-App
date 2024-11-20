<template>
	<v-app>
		<router-view v-slot="{ Component }">
			<transition name="slide-fade">
				<component :is="Component" />
			</transition>
		</router-view>
	</v-app>
</template>

<script>
export default {

}
</script>