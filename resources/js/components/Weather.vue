<template>
    <div class="site-content">
        <div class="hero" data-bg-image="images/banner.png">
            <div class="container">
                <form class="find-location">
                    <input type="search" id="city" style="width: 1090px;" placeholder="Find your location..." />
                    <strong id="address-value" style="visibility: hidden;">none</strong>
                </form>
            </div>
        </div>
        <div class="forecast-table">
            <div class="container">
                <div class="forecast-container">
                    <div class="today forecast">
                        <div class="forecast-content">
                            <div class="location">{{ location.name }}</div>
                            <div class="degree">
                                <div class="num">{{ currentTemperature.actual }}<sup>o</sup>C</div>
                                <div class="forecast-icon">
                                    <canvas id="iconCurrent" width="100" height="100"></canvas>
                                </div>
								<h6>Feels like {{ currentTemperature.feels }}<sup>o</sup>C</h6>
                            </div>
                        </div>
                    </div>
                    <div v-for="(day, index) in dailyFiveDays" :key="day.time" class="forecast">
						<div class="forecast-header">
							<div class="day">{{ toDayOfWeek(day.time) }}</div>
						</div>
						<div class="forecast-content">
							<div class="forecast-icon">
							    <canvas :id="`icon${index + 1}`" :data-icon="day.icon" width="48" height="35"></canvas>
							</div>
							<div class="degree">{{ Math.round(day.temperatureMax) }}<sup>o</sup>C</div>
							<small>{{ Math.round(day.temperatureMin) }}<sup>o</sup></small>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        this.fetchData()

        const placesAutocomplete = places({
            appId: 'pl2VFDBC4T1F',
            apiKey: '6aaffac13624150fd7afb97f778bc447',
            container: document.querySelector('#city'),
        }).configure({
            type: 'city',
            aroundLatLngViaIP: false,
        });

        const $address = document.querySelector('#address-value')
        placesAutocomplete.on('change', (e) => {
            $address.textContent = e.suggestion.value

            this.location.name = `${e.suggestion.name}, ${e.suggestion.country}`
            this.location.latitude = e.suggestion.latlng.lat
            this.location.longitude = e.suggestion.latlng.lng
        });

        placesAutocomplete.on('clear', function() {
            $address.textContent = 'none';
        });

    },
	data() {
		return {
			currentTemperature: {
				actual: '',
				feels: '',
				summary: '',
                icon: '',
                time: ''
            },
            daily: [],
			location: {
				name: 'Baku, Azerbaijan',
				latitude: '40.4093',
				longitude: '49.8671'
			}
		}
    },
    watch: {
        location: {
            handler() {
                this.fetchData();
            },
            deep: true
        }
    },
    computed: {
        dailyFiveDays() {
            return this.daily.filter((day, index) => index < 5 )
        }
    },
	methods: {
		fetchData() {
            const skycons = new Skycons({'color': '#00b8de'});

			fetch(`/api/weather?latitude=${this.location.latitude}&longitude=${this.location.longitude}`)
				.then(response => response.json())
				.then(data => {
					this.currentTemperature.actual = Math.round(data.currently.temperature);
					this.currentTemperature.feels = Math.round(data.currently.apparentTemperature);
					this.currentTemperature.summary = data.currently.summary;
                    this.currentTemperature.icon = data.currently.icon;
                    this.currentTemperature.time = data.currently.time;
                    this.daily = data.daily.data;
                    
                    skycons.add('iconCurrent', this.currentTemperature.icon);
                    skycons.play();

                    this.$nextTick(() => {
                        skycons.add('icon1', $('#icon1')[0].getAttribute('data-icon'));
                        skycons.add('icon2', $('#icon2')[0].getAttribute('data-icon'));
                        skycons.add('icon3', $('#icon3')[0].getAttribute('data-icon'));
                        skycons.add('icon4', $('#icon4')[0].getAttribute('data-icon'));
                        skycons.add('icon5', $('#icon5')[0].getAttribute('data-icon'));
                        skycons.play();                        
                    })
				});
        },
        toDayOfWeek(timestamp) {
            const date = new Date(timestamp * 1000);
            const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            return daysOfWeek[date.getDay()];
        }
	}
};
</script>
