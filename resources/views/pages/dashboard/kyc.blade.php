
<!-- Force Profile Modal -->
<div class="modal fade" id="forceProfileModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content shadow-lg border-0 rounded-0 rounded-md-4">
			<div class="modal-header bg-primary text-white rounded-0 rounded-top-md-4">
				<h5 class="modal-title text-white"><i class="fas fa-user-check me-2"></i>Complete Your Profile</h5>
			</div>
			<div class="modal-body p-4">
				<div class="text-center mb-4">
					<div class="avatar avatar-lg mb-3">
						<i class="fas fa-user-circle fa-3x text-primary"></i>
					</div>
					<h6 class="fw-bold">Welcome to Smart Idea! 🎉</h6>
					<p class="text-muted small">Please complete your profile to access all features</p>
				</div>
                
				@include('pages.alart')

				<form method="POST" action="{{ route('profile.updateRequired') }}" class="needs-validation" novalidate>
					@csrf
					
					<!-- Personal Information Section -->
					<div class="card border-0 shadow-sm rounded-0 rounded-md-4 mb-4">
						<div class="card-body">
							<h6 class="text-primary mb-3 d-flex align-items-center">
								<i class="fas fa-user-circle me-2"></i>Personal Details<span class="text-danger">*</span>
							</h6>
							
							<div class="row g-3">
								<div class="col-md-4">
									<div class="form-floating">
										<input type="text" class="form-control" id="first_name" name="first_name" 
											value="{{ old('first_name', Auth::user()->first_name) }}" required
											placeholder="First Name">
										<label for="first_name">First Name</label>
									</div>
								</div>
								
								<div class="col-md-4">
									<div class="form-floating">
										<input type="text" class="form-control" id="last_name" name="last_name"
											value="{{ old('last_name', Auth::user()->last_name ?? '') }}" required
											placeholder="Last Name">
										<label for="last_name">Last Name</label>
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-floating">
										<input type="text" class="form-control" id="middle_name" name="middle_name"
											value="{{ old('middle_name', Auth::user()->middle_name ?? '') }}"
											placeholder="Middle Name">
										<label for="middle_name">Middle Name</label>
									</div>
								</div>

                                	<div class="col-md-4">
									<div class="form-floating">
										<input type="date" class="form-control" id="dateOfBirth" name="birthdate"
											value="{{ old('birthdate', Auth::user()->birthdate ?? '') }}" required
											placeholder="Date of Birth">
										<label for="dateOfBirth">Date of Birth</label>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-floating">
										<input type="tel" class="form-control" id="phone_no" name="phone_no"
											value="{{ old('phone_no', Auth::user()->phone_no ?? '') }}" minlength="11" maxlength="11" required
											placeholder="Phone Number">
										<label for="phone_no">Phone Number</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Address Section -->
					<div class="card border-0 shadow-sm rounded-0 rounded-md-4 mb-4">
						<div class="card-body">
							<h6 class="text-primary mb-3 d-flex align-items-center">
								<i class="fas fa-map-marker-alt me-2"></i>Address Information
							</h6>
							
							<div class="row g-3">
								<div class="col-md-4">
									<div class="mb-0">
										<label class="form-label fw-semibold" for="state">State <span class="text-danger">*</span></label>
                                        <select class="form-select" id="state" name="state" required>
                                            <option value="" disabled selected>Choose State</option>
                                            <option value="Abia">Abia</option>
                                            <option value="Adamawa">Adamawa</option>
                                            <option value="Akwa Ibom">Akwa Ibom</option>
                                            <option value="Anambra">Anambra</option>
                                            <option value="Bauchi">Bauchi</option>
                                            <option value="Bayelsa">Bayelsa</option>
                                            <option value="Benue">Benue</option>
                                            <option value="Borno">Borno</option>
                                            <option value="Cross River">Cross River</option>
                                            <option value="Delta">Delta</option>
                                            <option value="Ebonyi">Ebonyi</option>
                                            <option value="Edo">Edo</option>
                                            <option value="Ekiti">Ekiti</option>
                                            <option value="Enugu">Enugu</option>
                                            <option value="FCT">Federal Capital Territory</option>
                                            <option value="Gombe">Gombe</option>
                                            <option value="Imo">Imo</option>
                                            <option value="Jigawa">Jigawa</option>
                                            <option value="Kaduna">Kaduna</option>
                                            <option value="Kano">Kano</option>
                                            <option value="Katsina">Katsina</option>
                                            <option value="Kebbi">Kebbi</option>
                                            <option value="Kogi">Kogi</option>
                                            <option value="Kwara">Kwara</option>
                                            <option value="Lagos">Lagos</option>
                                            <option value="Nasarawa">Nasarawa</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Ogun">Ogun</option>
                                            <option value="Ondo">Ondo</option>
                                            <option value="Osun">Osun</option>
                                            <option value="Oyo">Oyo</option>
                                            <option value="Plateau">Plateau</option>
                                            <option value="Rivers">Rivers</option>
                                            <option value="Sokoto">Sokoto</option>
                                            <option value="Taraba">Taraba</option>
                                            <option value="Yobe">Yobe</option>
                                            <option value="Zamfara">Zamfara</option>
                                        </select>
                                        <div class="invalid-feedback">Please select your state.</div>
                                    </div>
								</div>
								
								<div class="col-md-4">
									<div class="mb-0">
                                        <label class="form-label fw-semibold" for="lga">LGA <span class="text-danger">*</span></label>
                                        <select class="form-select" id="lga" name="lga" required disabled>
                                            <option value="" disabled selected>Select State First</option>
                                        </select>
                                        <div class="invalid-feedback">Please select your LGA.</div>
                                    </div>
								</div>
								
								<div class="col-md-4">
									<div class="mb-0">
										 <label class="form-label fw-semibold" for="address">Address <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="address" name="address"
											value="{{ old('address', Auth::user()->address ?? '') }}" required
											placeholder="Address">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Verification Section -->
					<div class="card border-0 shadow-sm rounded-0 rounded-md-4 mb-4">
						<div class="card-body">
							<h6 class="text-primary mb-3 d-flex align-items-center">
								<i class="fas fa-shield-alt me-2"></i>Verification Details<span class="text-danger">*</span>
							</h6>
							
							<div class="row g-3">
								<div class="col-md-6">
									<div class="form-floating">
										<input type="text" class="form-control" id="bvn" name="bvn"
											value="{{ old('bvn', Auth::user()->bvn ?? '') }}" required maxlength="11" minlength="11"
											placeholder="BVN">
										<label for="bvn">BVN Number (11 digits)</label>
								    </div>
						        </div>
							</div>
						</div>
					</div>

					<!-- Transaction Pin -->
					<div class="card border-0 shadow-sm rounded-0 rounded-md-4 mb-4">
						<div class="card-body">
							<h6 class="text-primary mb-3 d-flex align-items-center">
								<i class="fas fa-key me-2"></i>Transaction PIN<span class="text-danger">*</span>
							</h6>

							<div class="row g-3">
								<div class="col-md-12">
									<div class="form-floating">
										<input type="password" class="form-control" id="transaction_pin" name="pin"
											inputmode="numeric" pattern="[0-9]{5}" minlength="5" maxlength="5" required
											placeholder="5-digit PIN" aria-describedby="pinHelp">
										<label for="transaction_pin">5-digit Transaction PIN</label>
									</div>
									<div id="pinHelp" class="form-text small text-muted mt-2">
										Create a secure 5-digit numeric PIN for your transactions. Do not share this PIN with anyone.
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /Transaction Pin -->

					<!-- Terms and Submit -->
					<div class="form-check mb-4">
						<input class="form-check-input" type="checkbox" id="termsCheck" name="termsCheck" required>
						<label class="form-check-label small" for="termsCheck">
							I agree to the <a href="#" class="text-primary">terms and conditions</a>
						</label>
						<div class="invalid-feedback">
							You must agree to the terms and conditions to proceed.
						</div>
					</div>

					<button id="complete-profile-btn" class="btn btn-primary w-100 py-3 fw-medium" type="submit">
						<i class="fas fa-check-circle me-2"></i>Complete Profile
					</button>
				</form>

				<div class="text-center mt-3">
					<form method="POST" action="{{ route('logout') }}" id="kyc-logout-form">
						@csrf
						<button type="button" class="btn btn-link text-decoration-none text-danger fw-medium" onclick="confirmLogout(event, 'kyc-logout-form')">
							<i class="fas fa-sign-out-alt me-1"></i>Logout
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Force Profile Modal -->

@push('scripts')
@php
    $user = Auth::user();
    $needsUpdate = !$user->bvn || !$user->phone_no || !$user->lga || !$user->state || !$user->pin || !$user->first_name || !$user->last_name;
@endphp

@if($needsUpdate)
    <script>
        const lgas = {
            "Abia": ["Aba North", "Aba South", "Arochukwu", "Bende", "Ikwuano", "Isiala Ngwa North", "Isiala Ngwa South", "Isuikwuato", "Obi Ngwa", "Ohafia", "Osisioma", "Ugwunagbo", "Ukwa East", "Ukwa West", "Umuahia North", "Umuahia South", "Umu Nneochi"],
            "Adamawa": ["Demsa", "Fufore", "Ganye", "Girei", "Gombi", "Guyuk", "Hong", "Jada", "Lamurde", "Madagali", "Maiha", "Mayo Belwa", "Michika", "Mubi North", "Mubi South", "Numan", "Shelleng", "Song", "Toungo", "Yola North", "Yola South"],
            "Akwa Ibom": ["Abak", "Eastern Obolo", "Eket", "Esit Eket", "Essien Udim", "Etim Ekpo", "Etinan", "Ibeno", "Ibesikpo Asutan", "Ibiono Ibom", "Ika", "Ikono", "Ikot Abasi", "Ikot Ekpene", "Ini", "Itu", "Mbo", "Mkpat Enin", "Nsit Atai", "Nsit Ibom", "Nsit Ubium", "Obot Akara", "Okobo", "Onna", "Oron", "Oruk Anam", "Udung Uko", "Ukanafun", "Uruan", "Urue-Offong/Oruko", "Uyo"],
            "Anambra": ["Aguata", "Anambra East", "Anambra West", "Anaocha", "Awka North", "Awka South", "Ayamelum", "Dunukofia", "Ekwusigo", "Idemili North", "Idemili South", "Ihiala", "Njikoka", "Nnewi North", "Nnewi South", "Ogbaru", "Onitsha North", "Onitsha South", "Orumba North", "Orumba South", "Oyi"],
            "Bauchi": ["Alkaleri", "Bauchi", "Bogoro", "Damban", "Darazo", "Dass", "Gamawa", "Ganjuwa", "Giade", "Itas/Gadau", "Jama'are", "Katagum", "Kirfi", "Misau", "Ningi", "Shira", "Tafawa Balewa", "Toro", "Warji", "Zaki"],
            "Bayelsa": ["Brass", "Ekeremor", "Kolokuma/Opokuma", "Nembe", "Ogbia", "Sagbama", "Southern Ijaw", "Yenagoa"],
            "Benue": ["Ado", "Agatu", "Apa", "Buruku", "Gboko", "Guma", "Gwer East", "Gwer West", "Katsina-Ala", "Konshisha", "Kwande", "Logo", "Makurdi", "Obi", "Ogbadibo", "Ohimini", "Oju", "Okpokwu", "Otukpo", "Tarka", "Ukum", "Ushongo", "Vandeikya"],
            "Borno": ["Abadam", "Askira/Uba", "Bama", "Bayo", "Biu", "Chibok", "Damboa", "Dikwa", "Gubio", "Guzamala", "Gwoza", "Hawul", "Jere", "Kaga", "Kala/Balge", "Konduga", "Kukawa", "Kwaya Kusar", "Mafa", "Magumeri", "Maiduguri", "Marte", "Mobbar", "Monguno", "Ngala", "Nganzai", "Shani"],
            "Cross River": ["Abi", "Akamkpa", "Akpabuyo", "Bakassi", "Bekwarra", "Biase", "Boki", "Calabar Municipal", "Calabar South", "Etung", "Ikom", "Obanliku", "Obubra", "Obudu", "Odukpani", "Ogoja", "Yakurr", "Yala"],
            "Delta": ["Aniocha North", "Aniocha South", "Bomadi", "Burutu", "Ethiope East", "Ethiope West", "Ika North East", "Ika South", "Isoko North", "Isoko South", "Ndokwa East", "Ndokwa West", "Okpe", "Oshimili North", "Oshimili South", "Patani", "Sapele", "Udu", "Ughelli North", "Ughelli South", "Ukwuani", "Uvwie", "Warri North", "Warri South", "Warri South West"],
            "Ebonyi": ["Abakaliki", "Afikpo North", "Afikpo South", "Ebonyi", "Ezza North", "Ezza South", "Ikwo", "Ishielu", "Ivo", "Izzi", "Ohaozara", "Ohaukwu", "Onicha"],
            "Edo": ["Akoko-Edo", "Egor", "Esan Central", "Esan North-East", "Esan South-East", "Esan West", "Etsako Central", "Etsako East", "Etsako West", "Igueben", "Ikpoba-Okha", "Oredo", "Orhionmwon", "Ovia North-East", "Ovia South-West", "Owan East", "Owan West", "Uhunmwonde"],
            "Ekiti": ["Ado Ekiti", "Efon", "Ekiti East", "Ekiti South-West", "Ekiti West", "Emure", "Gbonyin", "Ido Osi", "Ijero", "Ikere", "Ikole", "Ilejemeje", "Irepodun/Ifelodun", "Ise/Orun", "Moba", "Oye"],
            "Enugu": ["Aninri", "Awgu", "Enugu East", "Enugu North", "Enugu South", "Ezeagu", "Igbo Etiti", "Igbo Eze North", "Igbo Eze South", "Isi Uzo", "Nkanu East", "Nkanu West", "Nsukka", "Oji River", "Udenu", "Udi", "Uzo Uwani"],
            "FCT": ["Abaji", "Bwari", "Gwagwalada", "Kuje", "Kwali", "Municipal Area Council"],
            "Gombe": ["Akko", "Balanga", "Billiri", "Dukku", "Funakaye", "Gombe", "Kaltungo", "Kwami", "Nafada", "Shongom", "Yamaltu/Deba"],
            "Imo": ["Aboh Mbaise", "Ahiazu Mbaise", "Ehime Mbano", "Ezinihitte", "Ideato North", "Ideato South", "Ihitte/Uboma", "Ikeduru", "Isiala Mbano", "Isu", "Mbaitoli", "Ngor Okpala", "Njaba", "Nkwerre", "Nwangele", "Obowo", "Oguta", "Ohaji/Egbema", "Okigwe", "Orlu", "Orsu", "Oru East", "Oru West", "Owerri Municipal", "Owerri North", "Owerri West"],
            "Jigawa": ["Auyo", "Babura", "Biriniwa", "Birnin Kudu", "Buji", "Dutse", "Gagarawa", "Garki", "Gumel", "Guri", "Gwaram", "Gwiwa", "Hadejia", "Jahun", "Kafin Hausa", "Kaugama", "Kazaure", "Kiri Kasama", "Kiyawa", "Maigatari", "Malam Madori", "Miga", "Ringim", "Roni", "Sule Tankarkar", "Taura", "Yankwashi"],
            "Kaduna": ["Birnin Gwari", "Chikun", "Giwa", "Igabi", "Ikara", "Jaba", "Jema'a", "Kachia", "Kaduna North", "Kaduna South", "Kagarko", "Kajuru", "Kaura", "Kauru", "Kubau", "Kudan", "Lere", "Makarfi", "Sabon Gari", "Sanga", "Soba", "Zangon Kataf", "Zaria"],
            "Kano": ["Ajingi", "Albasu", "Bagwai", "Bebeji", "Bichi", "Bunkure", "Dala", "Dambatta", "Dawakin Kudu", "Dawakin Tofa", "Doguwa", "Fagge", "Gabasawa", "Garko", "Garun Mallam", "Gaya", "Gezawa", "Gwale", "Gwarzo", "Kabo", "Kano Municipal", "Karaye", "Kibiya", "Kiru", "Kumbotso", "Kunchi", "Kura", "Madobi", "Makoda", "Minjibir", "Nasarawa", "Rano", "Rimin Gado", "Rogo", "Shanono", "Sumaila", "Takai", "Tarauni", "Tofa", "Tsanyawa", "Tudun Wada", "Ungogo", "Warawa", "Wudil"],
            "Katsina": ["Bakori", "Batagarawa", "Batsari", "Baure", "Bindawa", "Charanchi", "Dandume", "Danja", "Dan Musa", "Daura", "Dutsi", "Dutsin Ma", "Faskari", "Funtua", "Ingawa", "Jibia", "Kafur", "Kaita", "Kankara", "Kankia", "Katsina", "Kurfi", "Kusada", "Mai'Adua", "Malumfashi", "Mani", "Mashi", "Matazu", "Musawa", "Rimi", "Sabuwa", "Safana", "Sandamu", "Zango"],
            "Kebbi": ["Aleiro", "Arewa Dandi", "Argungu", "Augie", "Bagudo", "Birnin Kebbi", "Bunza", "Dandi", "Fakai", "Gwandu", "Jega", "Kalgo", "Koko/Besse", "Maiyama", "Ngaski", "Sakaba", "Shanga", "Suru", "Wasagu/Danko", "Yauri", "Zuru"],
            "Kogi": ["Adavi", "Ajaokuta", "Ankpa", "Bassa", "Dekina", "Ibaji", "Idah", "Igalamela Odolu", "Ijumu", "Kabba/Bunu", "Kogi", "Lokoja", "Mopa-Muro", "Ofu", "Ogori/Magongo", "Okehi", "Okene", "Olamaboro", "Omala", "Yagba East", "Yagba West"],
            "Kwara": ["Asa", "Baruten", "Edu", "Ekiti", "Ifelodun", "Ilorin East", "Ilorin South", "Ilorin West", "Irepodun", "Isin", "Kaiama", "Moro", "Offa", "Oke Ero", "Oyun", "Pategi"],
            "Lagos": ["Agege", "Ajeromi-Ifelodun", "Alimosho", "Amuwo-Odofin", "Apapa", "Badagry", "Epe", "Eti Osa", "Ibeju-Lekki", "Ifako-Ijaiye", "Ikeja", "Ikorodu", "Kosofe", "Lagos Island", "Lagos Mainland", "Mushin", "Ojo", "Oshodi-Isolo", "Shomolu", "Surulere"],
            "Nasarawa": ["Akwanga", "Awe", "Doma", "Karu", "Keana", "Keffi", "Kokona", "Lafia", "Nasarawa", "Nasarawa Egon", "Obi", "Toto", "Wamba"],
            "Niger": ["Agaie", "Agwara", "Bida", "Borgu", "Bosso", "Chanchaga", "Edati", "Gbako", "Gurara", "Katcha", "Kontagora", "Lapai", "Lavun", "Magama", "Mariga", "Mashegu", "Mokwa", "Munya", "Paikoro", "Rafi", "Rijau", "Shiroro", "Suleja", "Tafa", "Wushishi"],
            "Ogun": ["Abeokuta North", "Abeokuta South", "Ado-Odo/Ota", "Egbado North", "Egbado South", "Ewekoro", "Ifo", "Ijebu East", "Ijebu North", "Ijebu North East", "Ijebu Ode", "Ikenne", "Imeko Afon", "Ipokia", "Obafemi Owode", "Odeda", "Odogbolu", "Ogun Waterside", "Remo North", "Shagamu"],
            "Ondo": ["Akoko North-East", "Akoko North-West", "Akoko South-East", "Akoko South-West", "Akure North", "Akure South", "Ese Odo", "Idanre", "Ifedore", "Ilaje", "Ile Oluji/Okeigbo", "Irele", "Odigbo", "Okitipupa", "Ondo East", "Ondo West", "Ose", "Owo"],
            "Osun": ["Aiyedaade", "Aiyedire", "Atakumosa East", "Atakumosa West", "Boluwaduro", "Boripe", "Ede North", "Ede South", "Egbedore", "Ejigbo", "Ife Central", "Ife East", "Ife North", "Ife South", "Ifedayo", "Ifelodun", "Ila", "Ilesa East", "Ilesa West", "Irepodun", "Irewole", "Isokan", "Iwo", "Obokun", "Odo Otin", "Ola Oluwa", "Olorunda", "Oriade", "Orolu", "Osogbo"],
            "Oyo": ["Afijio", "Akinyele", "Atiba", "Atisbo", "Egbeda", "Ibadan North", "Ibadan North-East", "Ibadan North-West", "Ibadan South-East", "Ibadan South-West", "Ibarapa Central", "Ibarapa East", "Ibarapa North", "Ido", "Irepo", "Iseyin", "Itesiwaju", "Iwajowa", "Kajola", "Lagelu", "Ogbomosho North", "Ogbomosho South", "Ogo Oluwa", "Olorunsogo", "Oluyole", "Ona Ara", "Orelope", "Ori Ire", "Oyo East", "Oyo West", "Saki East", "Saki West", "Surulere"],
            "Plateau": ["Barkin Ladi", "Bassa", "Bokkos", "Jos East", "Jos North", "Jos South", "Kanam", "Kanke", "Langtang North", "Langtang South", "Mangu", "Mikang", "Pankshin", "Qua'an Pan", "Riyom", "Shendam", "Wase"],
            "Rivers": ["Abua/Odual", "Ahoada East", "Ahoada West", "Akuku-Toru", "Andoni", "Asari-Toru", "Bonny", "Degema", "Eleme", "Emohua", "Etche", "Gokana", "Ikwerre", "Khana", "Obio/Akpor", "Ogba/Egbema/Ndoni", "Ogu/Bolo", "Okrika", "Omuma", "Opobo/Nkoro", "Oyigbo", "Port Harcourt", "Tai"],
            "Sokoto": ["Binji", "Bodinga", "Dange Shuni", "Gada", "Goronyo", "Gudu", "Gwadabawa", "Illela", "Isa", "Kebbe", "Kware", "Rabah", "Sabon Birni", "Shagari", "Silame", "Sokoto North", "Sokoto South", "Tambuwal", "Tangaza", "Tureta", "Wamako", "Wurno", "Yabo"],
            "Taraba": ["Ardo Kola", "Bali", "Donga", "Gashaka", "Gassol", "Ibi", "Jalingo", "Karim Lamido", "Kumi", "Lau", "Sardauna", "Takum", "Ussa", "Wukari", "Yorro", "Zing"],
            "Yobe": ["Bade", "Bursari", "Damaturu", "Fika", "Fune", "Geidam", "Gujba", "Gulani", "Jakusko", "Karasuwa", "Machina", "Nangere", "Nguru", "Potiskum", "Tarmuwa", "Yunusari", "Yusufari"],
            "Zamfara": ["Anka", "Bakura", "Birnin Magaji/Kiyaw", "Bukkuyum", "Bungudu", "Gummi", "Gusau", "Kaura Namoda", "Maradun", "Maru", "Shinkafi", "Talata Mafara", "Chafe", "Zurmi"]
        };

        const stateSelect = document.getElementById('state');
        const lgaSelect = document.getElementById('lga');
        const userState = "{{ old('state', Auth::user()->state ?? '') }}";
        const userLga = "{{ old('lga', Auth::user()->lga ?? '') }}";

        function populateLgas(state) {
            lgaSelect.innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.selected = true;
            defaultOption.disabled = true;
            defaultOption.value = "";
            defaultOption.textContent = "Choose LGA";
            lgaSelect.appendChild(defaultOption);
            
            if (state && lgas[state]) {
                lgas[state].forEach(function(lga) {
                    const option = document.createElement('option');
                    option.value = lga;
                    option.textContent = lga;
                    lgaSelect.appendChild(option);
                });
                lgaSelect.disabled = false;
            } else {
                lgaSelect.disabled = true;
            }
        }

        stateSelect.addEventListener('change', function() {
            populateLgas(this.value);
        });

        if (userState) {
            stateSelect.value = userState;
            populateLgas(userState);
            if (userLga) {
                lgaSelect.value = userLga;
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            new bootstrap.Modal(document.getElementById('forceProfileModal')).show();
        });
    </script>
@endif
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 4000);
    });

    function confirmLogout(event, formId) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d5c3e',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endpush
