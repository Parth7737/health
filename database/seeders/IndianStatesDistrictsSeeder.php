<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndianStatesDistrictsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('indian_districts')->truncate();
        DB::table('indian_states')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = $this->getData();

        foreach ($data as $stateData) {
            $stateId = DB::table('indian_states')->insertGetId([
                'name'      => $stateData['name'],
                'code'      => $stateData['code'],
                'is_active' => 1,
            ]);

            $districts = array_map(fn ($d) => [
                'state_id'  => $stateId,
                'name'      => $d,
                'is_active' => 1,
            ], $stateData['districts']);

            foreach (array_chunk($districts, 50) as $chunk) {
                DB::table('indian_districts')->insert($chunk);
            }
        }
    }

    private function getData(): array
    {
        return [
            ['name' => 'Andhra Pradesh', 'code' => 'AP', 'districts' => [
                'Alluri Sitharama Raju','Anakapalli','Ananthapuramu','Annamayya','Bapatla','Chittoor','East Godavari','Eluru','Guntur','Kakinada','Krishna','Kurnool','Nandyal','Nellore','NTR','Palnadu','Parvathipuram Manyam','Prakasam','Sri Potti Sriramulu Nellore','Sri Sathya Sai','Srikakulam','Tirupati','Vizianagaram','Visakhapatnam','West Godavari','YSR Kadapa'
            ]],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR', 'districts' => [
                'Anjaw','Changlang','Dibang Valley','East Kameng','East Siang','Kamle','Kra Daadi','Kurung Kumey','Lepa Rada','Lohit','Longding','Lower Dibang Valley','Lower Siang','Lower Subansiri','Namsai','Pakke-Kessang','Papum Pare','Shi Yomi','Siang','Tawang','Tirap','Upper Siang','Upper Subansiri','West Kameng','West Siang'
            ]],
            ['name' => 'Assam', 'code' => 'AS', 'districts' => [
                'Bajali','Baksa','Barpeta','Biswanath','Bongaigaon','Cachar','Charaideo','Chirang','Darrang','Dhemaji','Dhubri','Dibrugarh','Dima Hasao','Goalpara','Golaghat','Hailakandi','Hojai','Jorhat','Kamrup','Kamrup Metropolitan','Karbi Anglong','Karimganj','Kokrajhar','Lakhimpur','Majuli','Morigaon','Nagaon','Nalbari','Sivasagar','Sonitpur','South Salmara-Mankachar','Tamulpur','Tinsukia','Udalguri','West Karbi Anglong'
            ]],
            ['name' => 'Bihar', 'code' => 'BR', 'districts' => [
                'Araria','Arwal','Aurangabad','Banka','Begusarai','Bhagalpur','Bhojpur','Buxar','Darbhanga','East Champaran','Gaya','Gopalganj','Jamui','Jehanabad','Kaimur','Katihar','Khagaria','Kishanganj','Lakhisarai','Madhepura','Madhubani','Munger','Muzaffarpur','Nalanda','Nawada','Patna','Purnia','Rohtas','Saharsa','Samastipur','Saran','Sheikhpura','Sheohar','Sitamarhi','Siwan','Supaul','Vaishali','West Champaran'
            ]],
            ['name' => 'Chhattisgarh', 'code' => 'CG', 'districts' => [
                'Balod','Baloda Bazar','Balrampur','Bastar','Bemetara','Bijapur','Bilaspur','Dantewada','Dhamtari','Durg','Gariaband','Gaurela-Pendra-Marwahi','Janjgir-Champa','Jashpur','Kabirdham','Kanker','Khairagarh','Kondagaon','Korba','Koriya','Mahasamund','Manendragarh','Mohla-Manpur','Mungeli','Narayanpur','Raigarh','Raipur','Rajnandgaon','Sarangarh-Bilaigarh','Sukma','Surajpur','Surguja'
            ]],
            ['name' => 'Goa', 'code' => 'GA', 'districts' => [
                'North Goa','South Goa'
            ]],
            ['name' => 'Gujarat', 'code' => 'GJ', 'districts' => [
                'Ahmedabad','Amreli','Anand','Aravalli','Banaskantha','Bharuch','Bhavnagar','Botad','Chhota Udaipur','Dahod','Dang','Devbhoomi Dwarka','Gandhinagar','Gir Somnath','Jamnagar','Junagadh','Kheda','Kutch','Mahisagar','Mehsana','Morbi','Narmada','Navsari','Panchmahal','Patan','Porbandar','Rajkot','Sabarkantha','Surat','Surendranagar','Tapi','Vadodara','Valsad'
            ]],
            ['name' => 'Haryana', 'code' => 'HR', 'districts' => [
                'Ambala','Bhiwani','Charkhi Dadri','Faridabad','Fatehabad','Gurugram','Hisar','Jhajjar','Jind','Kaithal','Karnal','Kurukshetra','Mahendragarh','Nuh','Palwal','Panchkula','Panipat','Rewari','Rohtak','Sirsa','Sonipat','Yamunanagar'
            ]],
            ['name' => 'Himachal Pradesh', 'code' => 'HP', 'districts' => [
                'Bilaspur','Chamba','Hamirpur','Kangra','Kinnaur','Kullu','Lahaul and Spiti','Mandi','Shimla','Sirmaur','Solan','Una'
            ]],
            ['name' => 'Jharkhand', 'code' => 'JH', 'districts' => [
                'Bokaro','Chatra','Deoghar','Dhanbad','Dumka','East Singhbhum','Garhwa','Giridih','Godda','Gumla','Hazaribagh','Jamtara','Khunti','Koderma','Latehar','Lohardaga','Pakur','Palamu','Ramgarh','Ranchi','Sahebganj','Seraikela-Kharsawan','Simdega','West Singhbhum'
            ]],
            ['name' => 'Karnataka', 'code' => 'KA', 'districts' => [
                'Bagalkote','Ballari','Belagavi','Bengaluru Rural','Bengaluru Urban','Bidar','Chamarajanagar','Chikkaballapura','Chikkamagaluru','Chitradurga','Dakshina Kannada','Davanagere','Dharwad','Gadag','Hassan','Haveri','Kalaburagi','Kodagu','Kolar','Koppal','Mandya','Mysuru','Raichur','Ramanagara','Shivamogga','Tumakuru','Udupi','Uttara Kannada','Vijayanagara','Vijayapura','Yadgir'
            ]],
            ['name' => 'Kerala', 'code' => 'KL', 'districts' => [
                'Alappuzha','Ernakulam','Idukki','Kannur','Kasaragod','Kollam','Kottayam','Kozhikode','Malappuram','Palakkad','Pathanamthitta','Thiruvananthapuram','Thrissur','Wayanad'
            ]],
            ['name' => 'Madhya Pradesh', 'code' => 'MP', 'districts' => [
                'Agar Malwa','Alirajpur','Anuppur','Ashoknagar','Balaghat','Barwani','Betul','Bhind','Bhopal','Burhanpur','Chhatarpur','Chhindwara','Damoh','Datia','Dewas','Dhar','Dindori','Guna','Gwalior','Harda','Hoshangabad','Indore','Jabalpur','Jhabua','Katni','Khandwa','Khargone','Mandla','Mandsaur','Morena','Narsinghpur','Neemuch','Niwari','Panna','Raisen','Rajgarh','Ratlam','Rewa','Sagar','Satna','Sehore','Seoni','Shahdol','Shajapur','Sheopur','Shivpuri','Sidhi','Singrauli','Tikamgarh','Ujjain','Umaria','Vidisha'
            ]],
            ['name' => 'Maharashtra', 'code' => 'MH', 'districts' => [
                'Ahmednagar','Akola','Amravati','Aurangabad','Beed','Bhandara','Buldhana','Chandrapur','Dhule','Gadchiroli','Gondia','Hingoli','Jalgaon','Jalna','Kolhapur','Latur','Mumbai City','Mumbai Suburban','Nagpur','Nanded','Nandurbar','Nashik','Osmanabad','Palghar','Parbhani','Pune','Raigad','Ratnagiri','Sangli','Satara','Sindhudurg','Solapur','Thane','Wardha','Washim','Yavatmal'
            ]],
            ['name' => 'Manipur', 'code' => 'MN', 'districts' => [
                'Bishnupur','Chandel','Churachandpur','Imphal East','Imphal West','Jiribam','Kakching','Kamjong','Kangpokpi','Noney','Pherzawl','Senapati','Tamenglong','Tengnoupal','Thoubal','Ukhrul'
            ]],
            ['name' => 'Meghalaya', 'code' => 'ML', 'districts' => [
                'East Garo Hills','East Jaintia Hills','East Khasi Hills','Eastern West Khasi Hills','North Garo Hills','Ri Bhoi','South Garo Hills','South West Garo Hills','South West Khasi Hills','West Garo Hills','West Jaintia Hills','West Khasi Hills'
            ]],
            ['name' => 'Mizoram', 'code' => 'MZ', 'districts' => [
                'Aizawl','Champhai','Hnahthial','Khawzawl','Kolasib','Lawngtlai','Lunglei','Mamit','Saiha','Saitual','Serchhip'
            ]],
            ['name' => 'Nagaland', 'code' => 'NL', 'districts' => [
                'Chumoukedima','Dimapur','Kiphire','Kohima','Longleng','Mokokchung','Mon','Noklak','Peren','Phek','Shamator','Tseminyü','Tuensang','Wokha','Zunheboto'
            ]],
            ['name' => 'Odisha', 'code' => 'OD', 'districts' => [
                'Angul','Balangir','Balasore','Bargarh','Bhadrak','Boudh','Cuttack','Deogarh','Dhenkanal','Gajapati','Ganjam','Jagatsinghpur','Jajpur','Jharsuguda','Kalahandi','Kandhamal','Kendrapara','Kendujhar','Khordha','Koraput','Malkangiri','Mayurbhanj','Nabarangpur','Nayagarh','Nuapada','Puri','Rayagada','Sambalpur','Sonepur','Sundargarh'
            ]],
            ['name' => 'Punjab', 'code' => 'PB', 'districts' => [
                'Amritsar','Barnala','Bathinda','Faridkot','Fatehgarh Sahib','Fazilka','Ferozepur','Gurdaspur','Hoshiarpur','Jalandhar','Kapurthala','Ludhiana','Malerkotla','Mansa','Moga','Mohali','Muktsar','Pathankot','Patiala','Rupnagar','Sangrur','Shahid Bhagat Singh Nagar','Tarn Taran'
            ]],
            ['name' => 'Rajasthan', 'code' => 'RJ', 'districts' => [
                'Ajmer','Alwar','Anupgarh','Balotra','Banswara','Baran','Barmer','Beawar','Bharatpur','Bhilwara','Bikaner','Bundi','Chittorgarh','Churu','Dausa','Deeg','Dholpur','Didwana-Kuchaman','Dudu','Dungarpur','Gangapur City','Hanumangarh','Jaipur','Jaipur Rural','Jaisalmer','Jalore','Jhalawar','Jhunjhunu','Jodhpur','Jodhpur Rural','Karauli','Kekri','Khairthal-Tijara','Kotputli-Behror','Kota','Nagaur','Neem ka Thana','Pali','Phalodi','Pratapgarh','Rajsamand','Salumbar','Sanchore','Sawai Madhopur','Shahpura','Sikar','Sirohi','Sri Ganganagar','Tonk','Udaipur'
            ]],
            ['name' => 'Sikkim', 'code' => 'SK', 'districts' => [
                'East Sikkim','North Sikkim','Pakyong','Soreng','South Sikkim','West Sikkim'
            ]],
            ['name' => 'Tamil Nadu', 'code' => 'TN', 'districts' => [
                'Ariyalur','Chengalpattu','Chennai','Coimbatore','Cuddalore','Dharmapuri','Dindigul','Erode','Kallakurichi','Kancheepuram','Kanyakumari','Karur','Krishnagiri','Madurai','Mayiladuthurai','Nagapattinam','Namakkal','Nilgiris','Perambalur','Pudukkottai','Ramanathapuram','Ranipet','Salem','Sivaganga','Tenkasi','Thanjavur','Theni','Thoothukudi','Tiruchirappalli','Tirunelveli','Tirupathur','Tiruppur','Tiruvallur','Tiruvannamalai','Tiruvarur','Vellore','Villupuram','Virudhunagar'
            ]],
            ['name' => 'Telangana', 'code' => 'TS', 'districts' => [
                'Adilabad','Bhadradri Kothagudem','Hanumakonda','Hyderabad','Jagtial','Jangaon','Jayashankar Bhupalpally','Jogulamba Gadwal','Kamareddy','Karimnagar','Khammam','Kumuram Bheem','Mahabubabad','Mahabubnagar','Mancherial','Medak','Medchal-Malkajgiri','Mulugu','Nagarkurnool','Nalgonda','Narayanpet','Nirmal','Nizamabad','Peddapalli','Rajanna Sircilla','Rangareddy','Sangareddy','Siddipet','Suryapet','Vikarabad','Wanaparthy','Warangal','Yadadri Bhuvanagiri'
            ]],
            ['name' => 'Tripura', 'code' => 'TR', 'districts' => [
                'Dhalai','Gomati','Khowai','North Tripura','Sepahijala','Sipahijala','South Tripura','Unakoti','West Tripura'
            ]],
            ['name' => 'Uttar Pradesh', 'code' => 'UP', 'districts' => [
                'Agra','Aligarh','Ambedkar Nagar','Amethi','Amroha','Auraiya','Ayodhya','Azamgarh','Baghpat','Bahraich','Ballia','Balrampur','Banda','Barabanki','Bareilly','Basti','Bhadohi','Bijnor','Budaun','Bulandshahr','Chandauli','Chitrakoot','Deoria','Etah','Etawah','Farrukhabad','Fatehpur','Firozabad','Gautam Buddha Nagar','Ghaziabad','Ghazipur','Gonda','Gorakhpur','Hamirpur','Hapur','Hardoi','Hathras','Jalaun','Jaunpur','Jhansi','Kannauj','Kanpur Dehat','Kanpur Nagar','Kasganj','Kaushambi','Kheri','Kushinagar','Lalitpur','Lucknow','Maharajganj','Mahoba','Mainpuri','Mathura','Mau','Meerut','Mirzapur','Moradabad','Muzaffarnagar','Pilibhit','Pratapgarh','Prayagraj','Rae Bareli','Rampur','Saharanpur','Sambhal','Sant Kabir Nagar','Shahjahanpur','Shamli','Shravasti','Siddharthnagar','Sitapur','Sonbhadra','Sultanpur','Unnao','Varanasi'
            ]],
            ['name' => 'Uttarakhand', 'code' => 'UK', 'districts' => [
                'Almora','Bageshwar','Chamoli','Champawat','Dehradun','Haridwar','Nainital','Pauri Garhwal','Pithoragarh','Rudraprayag','Tehri Garhwal','Udham Singh Nagar','Uttarkashi'
            ]],
            ['name' => 'West Bengal', 'code' => 'WB', 'districts' => [
                'Alipurduar','Bankura','Birbhum','Cooch Behar','Dakshin Dinajpur','Darjeeling','Hooghly','Howrah','Jalpaiguri','Jhargram','Kalimpong','Kolkata','Maldah','Murshidabad','Nadia','North 24 Parganas','Paschim Bardhaman','Paschim Medinipur','Purba Bardhaman','Purba Medinipur','Purulia','South 24 Parganas','Uttar Dinajpur'
            ]],
            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN', 'districts' => ['North and Middle Andaman','South Andaman','Nicobar']],
            ['name' => 'Chandigarh', 'code' => 'CH', 'districts' => ['Chandigarh']],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DD', 'districts' => ['Dadra and Nagar Haveli','Daman','Diu']],
            ['name' => 'Delhi', 'code' => 'DL', 'districts' => ['Central Delhi','East Delhi','New Delhi','North Delhi','North East Delhi','North West Delhi','Shahdara','South Delhi','South East Delhi','South West Delhi','West Delhi']],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK', 'districts' => ['Anantnag','Bandipora','Baramulla','Budgam','Doda','Ganderbal','Jammu','Kathua','Kishtwar','Kulgam','Kupwara','Poonch','Pulwama','Rajouri','Ramban','Reasi','Samba','Shopian','Srinagar','Udhampur']],
            ['name' => 'Ladakh', 'code' => 'LA', 'districts' => ['Kargil','Leh']],
            ['name' => 'Lakshadweep', 'code' => 'LD', 'districts' => ['Lakshadweep']],
            ['name' => 'Puducherry', 'code' => 'PY', 'districts' => ['Karaikal','Mahe','Puducherry','Yanam']],
        ];
    }
}
