<template>
	<v-sheet style="background:linear-gradient(0deg,#242424, #000000);" class="fill-height">
		<section>
			<v-container
				style="height:200px;" fill-height
				class="justify-center align-center pt-0 pt-md-16"
			>
				<v-row class="font-family-secondary text-center white--text text-h6 text-md-h5 custom-transform-class pt-16 pt-md-0">
					<v-col class="pa-0 px-1 text-md-end" cols="12" md="6">
						<span class="font-weight-black mr-n1">{{ titlePrefix.cronus }}</span>
						<span class="font-weight-light">{{ titlePrefix.art }}</span>
					</v-col>
					<v-col class="pa-0 text-md-start" cols="12" md="6">
						<span class="font-weight-black">{{ $t('terms-and-conditions-page.title') }}</span>
					</v-col>
				</v-row>
			</v-container>
		</section>
		<section>
			<v-container style="padding-bottom:150px;" class="px-8">
				<div>
					<div class="text-subtitle-1 text-md-h6 font-weight-black">Welcome to the CronusArt website. The Site is owned and operated by CronusArt. By using the Site, you agree to be bound by these Terms and Conditions (the "Terms"). If you do not agree to these Terms, please do not use the Site.</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">1. Use of the Site</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
The Site is for your personal use only. You may not use the Site for any commercial purpose without the express written consent of CronusArt.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">2. Intellectual Property</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
All content on the Site, including but not limited to text, graphics, logos, images, and software, is the property of CronusArt or its content suppliers and protected by international copyright and trademark laws. You may not use any content on the Site for any commercial purpose without the express written consent of CronusArt.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">3. Disclaimer of Warranties</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
The Site is provided on an "as is" and "as available" basis. CronusArt makes no representations or warranties of any kind, express or implied, as to the operation of the Site or the information, content, materials, or products included on the Site.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">4. Limitation of Liability</div>
					<div class="mt-10">
						<pre class="text-body-2 font-family-primary">
CronusArt will not be liable for any damages of any kind arising from the use of the Site, including but not limited to direct, indirect, incidental, punitive, and consequential damages.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">5. Governing Law</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
These Terms shall be governed and construed in accordance with the laws of the State of [State], United States, without giving effect to any principles of conflicts of law.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">6. Changes to the Terms</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
CronusArt reserves the right to change these Terms at any time. Any changes to the Terms will be posted on this page and will become effective upon posting. Your continued use of the Site after any changes to the Terms have been posted will constitute your acceptance of those changes.
					</pre>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">7. Miscellaneous</div>
					<div class="mt-10">
						<pre style="line-height:24px;" class="text-body-2 font-family-primary">
These Terms constitute the entire agreement between you and CronusArt with respect to the Site. If any provision of these Terms is found to be invalid or unenforceable, that provision will be enforced to the maximum extent possible, and the remaining provisions will remain in full force and effect.

Thank you for using the CronusArt website. If you have any questions or concerns about these Terms, please contact us at info@cronusart.com.
					</pre>
					</div>
				</div>
			</v-container>
		</section>
	</v-sheet>
</template>

<script>
export default {
	components: {
	},
	data(){
		return{
			titlePrefix:{
				cronus:"Cronus",
				art:"Art"
			}
		}
	}
}
</script>

<style scoped>
pre{
	white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
.custom-transform-class{
	text-transform: uppercase
}
</style>