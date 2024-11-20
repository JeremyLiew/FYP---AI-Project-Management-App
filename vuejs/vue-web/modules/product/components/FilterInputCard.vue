<template>
	<v-item-group
		v-model="value__"
		mandatory
		class="justify-center"
		@change="$emit('input', value__)"
	>
		<template v-for="item in items" :key="item.id">
			<v-item
				v-slot="{ active, toggle }"
				:value="item"
			>
				<div
					v-ripple
					:class="active ? 'custom-yellow--text' : '' "
					class="clickable px-5 py-1 text-subtitle-1"
					@click="toggle"
				>
					{{ item.name }}
				</div>
			</v-item>
		</template>
	</v-item-group>
</template>

<script>
export default {
	props: {
		value:{
			type: null,
			default: null,
		},
		items: {
			type: Array,
			default: () => []
		},
	},
	data(){
		return {
			value__: null,
		}
	},
	watch:{
		"value":{
			handler(){
				this.value__ = this.value
			},
			immediate: true,
		}
	},
}
</script>