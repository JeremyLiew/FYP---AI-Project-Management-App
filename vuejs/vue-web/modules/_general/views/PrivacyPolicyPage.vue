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
						<span class="font-weight-black">{{ $t('privacy-policy-page.title') }}</span>
					</v-col>
				</v-row>
			</v-container>
		</section>
		<section>
			<v-container style="padding-bottom:150px;" class="px-8">
				<div>
					<div class="text-subtitle-1 text-md-h6 font-weight-black">Information We Collect</div>
					<div style="line-height:24px;" class="mt-10 text-body-2 font-family-primary">
						We collect information about you in the following ways:
					</div>
					<div class="mt-10">
						<ul style="line-height:24px;" class="text-body-2 font-family-primary">
							<li>
								Information you provide to us: When you fill out a form on the Site, such as the contact us form, we collect the information you provide, such as your name, email address, and phone number.
							</li>
							<li>
								Information we collect automatically: When you use the Site, we may automatically collect information about your device and your use of the Site, such as your IP address, browser type, and the pages you visit.
							</li>
						</ul>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">How We Use Your Information</div>
					<div style="line-height:24px;" class="mt-10 text-body-2 font-family-primary">
						We use the information we collect about you for the following purposes:
					</div>
					<div class="mt-10">
						<ul style="line-height:24px;" class="text-body-2 font-family-primary">
							<li>
								To respond to your inquiries and fulfil your requests: We use the information you provide to us to respond to your inquiries and to fulfil your requests, such as to send you newsletters or to provide you with information about our watches.
							</li>
							<li>
								To improve the Site: We may use the information we collect to understand how people use the Site and to improve the Site.
							</li>
							<li>To comply with legal obligations: We may use the information we collect to comply with legal obligations, such as to respond to a subpoena or to protect the rights, property, or safety of CronusArt or others.</li>
						</ul>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">Sharing Your Information</div>
					<div style="line-height:24px;" class="mt-10 text-body-2 font-family-primary">
						We do not sell or rent your personal information to third parties. However, we may share your information with third parties in the following cases:
					</div>
					<div class="mt-10">
						<ul style="line-height:24px;" class="text-body-2 font-family-primary">
							<li>
								With service providers: We may share your information with service providers who perform services on our behalf, such as hosting, analytics, and email marketing.
							</li>
							<li>
								In response to legal requests: We may share your information in response to legal requests, such as subpoenas or court orders, or in response to a government request.
							</li>
							<li>To protect the rights, property, or safety of CronusArt or others: We may share your information to protect the rights, property, or safety of CronusArt or others.</li>
						</ul>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">Your Choices and Rights</div>
					<div style="line-height:24px;" class="mt-10 text-body-2 font-family-primary">
						You have the following choices and rights regarding your information:
					</div>
					<div class="mt-10">
						<ul style="line-height:24px;" class="text-body-2 font-family-primary">
							<li>
								Opt-out of marketing communications: You can opt-out of receiving marketing communications from us by following the unsubscribe instructions in the communication you receive.
							</li>
							<li>
								Access and update your information: You can access and update your personal information by contacting us using the contact information provided at the end of this Privacy Policy.
							</li>
							<li>Lodge a complaint: If you have a complaint about our use of your information, you can contact us using the contact information provided at the end of this Privacy Policy. You also have the right to file a complaint with a data protection authority.</li>
						</ul>
					</div>
				</div>
				<div class="mt-15">
					<div class="text-subtitle-1 text-md-h6 font-weight-black">Data Retention</div>
					<div style="line-height:24px;" class="mt-10 text-body-2 font-family-primary">
						We will retain your information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law.
						Changes to This Privacy Policy
						We may update this Privacy Policy from time to time. We will post any changes on this page and encourage you to review the Privacy Policy periodically. If we make any material changes, we will notify you by email or by means of a notice on the Site prior to the change becoming effective.
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