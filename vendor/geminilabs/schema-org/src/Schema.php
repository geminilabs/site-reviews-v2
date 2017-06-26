<?php

namespace GeminiLabs\SchemaOrg;

use GeminiLabs\SchemaOrg\Unknown;

/**
 * Factory class for all Schema.org types.
 *
 * @method static Thing thing( null|string $type )
 * @method static CreativeWork creativeWork( null|string $type )
 * @method static WebPage webPage( null|string $type )
 * @method static AboutPage aboutPage( null|string $type )
 * @method static Organization organization( null|string $type )
 * @method static Place place( null|string $type )
 * @method static LocalBusiness localBusiness( null|string $type )
 * @method static MedicalOrganization medicalOrganization( null|string $type )
 * @method static Dentist dentist( null|string $type )
 * @method static Hospital hospital( null|string $type )
 * @method static Pharmacy pharmacy( null|string $type )
 * @method static FinancialService financialService( null|string $type )
 * @method static ProfessionalService professionalService( null|string $type )
 * @method static LegalService legalService( null|string $type )
 * @method static AccountingService accountingService( null|string $type )
 * @method static AdministrativeArea administrativeArea( null|string $type )
 * @method static EntertainmentBusiness entertainmentBusiness( null|string $type )
 * @method static AdultEntertainment adultEntertainment( null|string $type )
 * @method static Intangible intangible( null|string $type )
 * @method static Offer offer( null|string $type )
 * @method static AggregateOffer aggregateOffer( null|string $type )
 * @method static Rating rating( null|string $type )
 * @method static AggregateRating aggregateRating( null|string $type )
 * @method static CivicStructure civicStructure( null|string $type )
 * @method static Airport airport( null|string $type )
 * @method static AmusementPark amusementPark( null|string $type )
 * @method static AnimalShelter animalShelter( null|string $type )
 * @method static Residence residence( null|string $type )
 * @method static ApartmentComplex apartmentComplex( null|string $type )
 * @method static Aquarium aquarium( null|string $type )
 * @method static ArtGallery artGallery( null|string $type )
 * @method static Article article( null|string $type )
 * @method static Attorney attorney( null|string $type )
 * @method static Audience audience( null|string $type )
 * @method static MediaObject mediaObject( null|string $type )
 * @method static AudioObject audioObject( null|string $type )
 * @method static AutomotiveBusiness automotiveBusiness( null|string $type )
 * @method static AutoBodyShop autoBodyShop( null|string $type )
 * @method static AutoDealer autoDealer( null|string $type )
 * @method static Store store( null|string $type )
 * @method static AutoPartsStore autoPartsStore( null|string $type )
 * @method static AutoRental autoRental( null|string $type )
 * @method static AutoRepair autoRepair( null|string $type )
 * @method static AutoWash autoWash( null|string $type )
 * @method static AutomatedTeller automatedTeller( null|string $type )
 * @method static FoodEstablishment foodEstablishment( null|string $type )
 * @method static Bakery bakery( null|string $type )
 * @method static BankOrCreditUnion bankOrCreditUnion( null|string $type )
 * @method static Barcode barcode( null|string $type )
 * @method static BarOrPub barOrPub( null|string $type )
 * @method static Beach beach( null|string $type )
 * @method static HealthAndBeautyBusiness healthAndBeautyBusiness( null|string $type )
 * @method static BeautySalon beautySalon( null|string $type )
 * @method static LodgingBusiness lodgingBusiness( null|string $type )
 * @method static BedAndBreakfast bedAndBreakfast( null|string $type )
 * @method static BikeStore bikeStore( null|string $type )
 * @method static Blog blog( null|string $type )
 * @method static BlogPosting blogPosting( null|string $type )
 * @method static Landform landform( null|string $type )
 * @method static BodyOfWater bodyOfWater( null|string $type )
 * @method static Book book( null|string $type )
 * @method static BookFormatType bookFormatType( null|string $type )
 * @method static BookStore bookStore( null|string $type )
 * @method static SportsActivityLocation sportsActivityLocation( null|string $type )
 * @method static BowlingAlley bowlingAlley( null|string $type )
 * @method static Brewery brewery( null|string $type )
 * @method static PlaceOfWorship placeOfWorship( null|string $type )
 * @method static BuddhistTemple buddhistTemple( null|string $type )
 * @method static BusStation busStation( null|string $type )
 * @method static BusStop busStop( null|string $type )
 * @method static Event event( null|string $type )
 * @method static BusinessEvent businessEvent( null|string $type )
 * @method static CafeOrCoffeeShop cafeOrCoffeeShop( null|string $type )
 * @method static Campground campground( null|string $type )
 * @method static Canal canal( null|string $type )
 * @method static Casino casino( null|string $type )
 * @method static CatholicChurch catholicChurch( null|string $type )
 * @method static Cemetery cemetery( null|string $type )
 * @method static CheckoutPage checkoutPage( null|string $type )
 * @method static ChildCare childCare( null|string $type )
 * @method static ChildrensEvent childrensEvent( null|string $type )
 * @method static Church church( null|string $type )
 * @method static City city( null|string $type )
 * @method static GovernmentBuilding governmentBuilding( null|string $type )
 * @method static CityHall cityHall( null|string $type )
 * @method static ClothingStore clothingStore( null|string $type )
 * @method static CollectionPage collectionPage( null|string $type )
 * @method static EducationalOrganization educationalOrganization( null|string $type )
 * @method static CollegeOrUniversity collegeOrUniversity( null|string $type )
 * @method static ComedyClub comedyClub( null|string $type )
 * @method static ComedyEvent comedyEvent( null|string $type )
 * @method static Comment comment( null|string $type )
 * @method static ComputerStore computerStore( null|string $type )
 * @method static ContactPage contactPage( null|string $type )
 * @method static StructuredValue structuredValue( null|string $type )
 * @method static ContactPoint contactPoint( null|string $type )
 * @method static Continent continent( null|string $type )
 * @method static ConvenienceStore convenienceStore( null|string $type )
 * @method static Corporation corporation( null|string $type )
 * @method static Country country( null|string $type )
 * @method static Courthouse courthouse( null|string $type )
 * @method static Crematorium crematorium( null|string $type )
 * @method static DanceEvent danceEvent( null|string $type )
 * @method static PerformingGroup performingGroup( null|string $type )
 * @method static DanceGroup danceGroup( null|string $type )
 * @method static DaySpa daySpa( null|string $type )
 * @method static DefenceEstablishment defenceEstablishment( null|string $type )
 * @method static DepartmentStore departmentStore( null|string $type )
 * @method static Quantity quantity( null|string $type )
 * @method static Distance distance( null|string $type )
 * @method static DryCleaningOrLaundry dryCleaningOrLaundry( null|string $type )
 * @method static Duration duration( null|string $type )
 * @method static EducationEvent educationEvent( null|string $type )
 * @method static HomeAndConstructionBusiness homeAndConstructionBusiness( null|string $type )
 * @method static Electrician electrician( null|string $type )
 * @method static ElectronicsStore electronicsStore( null|string $type )
 * @method static ElementarySchool elementarySchool( null|string $type )
 * @method static Embassy embassy( null|string $type )
 * @method static EmergencyService emergencyService( null|string $type )
 * @method static EmploymentAgency employmentAgency( null|string $type )
 * @method static Energy energy( null|string $type )
 * @method static EventVenue eventVenue( null|string $type )
 * @method static ExerciseGym exerciseGym( null|string $type )
 * @method static FastFoodRestaurant fastFoodRestaurant( null|string $type )
 * @method static Festival festival( null|string $type )
 * @method static FireStation fireStation( null|string $type )
 * @method static Florist florist( null|string $type )
 * @method static FoodEvent foodEvent( null|string $type )
 * @method static FurnitureStore furnitureStore( null|string $type )
 * @method static GardenStore gardenStore( null|string $type )
 * @method static GasStation gasStation( null|string $type )
 * @method static GatedResidenceCommunity gatedResidenceCommunity( null|string $type )
 * @method static GeneralContractor generalContractor( null|string $type )
 * @method static GeoCoordinates geoCoordinates( null|string $type )
 * @method static GeoShape geoShape( null|string $type )
 * @method static GeoCircle geoCircle( null|string $type )
 * @method static GolfCourse golfCourse( null|string $type )
 * @method static GovernmentOffice governmentOffice( null|string $type )
 * @method static GovernmentOrganization governmentOrganization( null|string $type )
 * @method static GroceryStore groceryStore( null|string $type )
 * @method static HVACBusiness hVACBusiness( null|string $type )
 * @method static HairSalon hairSalon( null|string $type )
 * @method static HardwareStore hardwareStore( null|string $type )
 * @method static HealthClub healthClub( null|string $type )
 * @method static HighSchool highSchool( null|string $type )
 * @method static HinduTemple hinduTemple( null|string $type )
 * @method static HobbyShop hobbyShop( null|string $type )
 * @method static HomeGoodsStore homeGoodsStore( null|string $type )
 * @method static Hostel hostel( null|string $type )
 * @method static Hotel hotel( null|string $type )
 * @method static HousePainter housePainter( null|string $type )
 * @method static IceCreamShop iceCreamShop( null|string $type )
 * @method static ImageGallery imageGallery( null|string $type )
 * @method static ImageObject imageObject( null|string $type )
 * @method static InsuranceAgency insuranceAgency( null|string $type )
 * @method static InternetCafe internetCafe( null|string $type )
 * @method static ItemAvailability itemAvailability( null|string $type )
 * @method static ItemList itemList( null|string $type )
 * @method static BreadcrumbList breadcrumbList( null|string $type )
 * @method static OfferCatalog offerCatalog( null|string $type )
 * @method static ItemPage itemPage( null|string $type )
 * @method static JewelryStore jewelryStore( null|string $type )
 * @method static JobPosting jobPosting( null|string $type )
 * @method static LakeBodyOfWater lakeBodyOfWater( null|string $type )
 * @method static LandmarksOrHistoricalBuildings landmarksOrHistoricalBuildings( null|string $type )
 * @method static Language language( null|string $type )
 * @method static ComputerLanguage computerLanguage( null|string $type )
 * @method static LegislativeBuilding legislativeBuilding( null|string $type )
 * @method static Library library( null|string $type )
 * @method static LiquorStore liquorStore( null|string $type )
 * @method static LiteraryEvent literaryEvent( null|string $type )
 * @method static Locksmith locksmith( null|string $type )
 * @method static Map map( null|string $type )
 * @method static MapCategoryType mapCategoryType( null|string $type )
 * @method static Mass mass( null|string $type )
 * @method static PeopleAudience peopleAudience( null|string $type )
 * @method static ScholarlyArticle scholarlyArticle( null|string $type )
 * @method static Specialty specialty( null|string $type )
 * @method static MensClothingStore mensClothingStore( null|string $type )
 * @method static MiddleSchool middleSchool( null|string $type )
 * @method static SoftwareApplication softwareApplication( null|string $type )
 * @method static MobileApplication mobileApplication( null|string $type )
 * @method static MobilePhoneStore mobilePhoneStore( null|string $type )
 * @method static Mosque mosque( null|string $type )
 * @method static Motel motel( null|string $type )
 * @method static MotorcycleDealer motorcycleDealer( null|string $type )
 * @method static MotorcycleRepair motorcycleRepair( null|string $type )
 * @method static Mountain mountain( null|string $type )
 * @method static Movie movie( null|string $type )
 * @method static MovieClip movieClip( null|string $type )
 * @method static MovieRentalStore movieRentalStore( null|string $type )
 * @method static MovieTheater movieTheater( null|string $type )
 * @method static MovingCompany movingCompany( null|string $type )
 * @method static Museum museum( null|string $type )
 * @method static MusicPlaylist musicPlaylist( null|string $type )
 * @method static MusicAlbum musicAlbum( null|string $type )
 * @method static MusicEvent musicEvent( null|string $type )
 * @method static MusicGroup musicGroup( null|string $type )
 * @method static MusicRecording musicRecording( null|string $type )
 * @method static MusicStore musicStore( null|string $type )
 * @method static MusicVenue musicVenue( null|string $type )
 * @method static MusicVideoObject musicVideoObject( null|string $type )
 * @method static NGO nGO( null|string $type )
 * @method static NailSalon nailSalon( null|string $type )
 * @method static NewsArticle newsArticle( null|string $type )
 * @method static NightClub nightClub( null|string $type )
 * @method static Photograph photograph( null|string $type )
 * @method static Notary notary( null|string $type )
 * @method static OceanBodyOfWater oceanBodyOfWater( null|string $type )
 * @method static OfferItemCondition offerItemCondition( null|string $type )
 * @method static OfficeEquipmentStore officeEquipmentStore( null|string $type )
 * @method static OutletStore outletStore( null|string $type )
 * @method static Painting painting( null|string $type )
 * @method static Park park( null|string $type )
 * @method static ParkingFacility parkingFacility( null|string $type )
 * @method static PawnShop pawnShop( null|string $type )
 * @method static PerformingArtsTheater performingArtsTheater( null|string $type )
 * @method static Person person( null|string $type )
 * @method static PetStore petStore( null|string $type )
 * @method static Playground playground( null|string $type )
 * @method static Plumber plumber( null|string $type )
 * @method static PoliceStation policeStation( null|string $type )
 * @method static Pond pond( null|string $type )
 * @method static PostOffice postOffice( null|string $type )
 * @method static PostalAddress postalAddress( null|string $type )
 * @method static Preschool preschool( null|string $type )
 * @method static Product product( null|string $type )
 * @method static ProfilePage profilePage( null|string $type )
 * @method static PublicSwimmingPool publicSwimmingPool( null|string $type )
 * @method static RVPark rVPark( null|string $type )
 * @method static RadioStation radioStation( null|string $type )
 * @method static RealEstateAgent realEstateAgent( null|string $type )
 * @method static RecyclingCenter recyclingCenter( null|string $type )
 * @method static Reservoir reservoir( null|string $type )
 * @method static Restaurant restaurant( null|string $type )
 * @method static RestrictedDiet restrictedDiet( null|string $type )
 * @method static Review review( null|string $type )
 * @method static RiverBodyOfWater riverBodyOfWater( null|string $type )
 * @method static RoofingContractor roofingContractor( null|string $type )
 * @method static SaleEvent saleEvent( null|string $type )
 * @method static School school( null|string $type )
 * @method static Sculpture sculpture( null|string $type )
 * @method static ScreeningEvent screeningEvent( null|string $type )
 * @method static SeaBodyOfWater seaBodyOfWater( null|string $type )
 * @method static SearchResultsPage searchResultsPage( null|string $type )
 * @method static SelfStorage selfStorage( null|string $type )
 * @method static ShoeStore shoeStore( null|string $type )
 * @method static ShoppingCenter shoppingCenter( null|string $type )
 * @method static SingleFamilyResidence singleFamilyResidence( null|string $type )
 * @method static WebPageElement webPageElement( null|string $type )
 * @method static SiteNavigationElement siteNavigationElement( null|string $type )
 * @method static SkiResort skiResort( null|string $type )
 * @method static SocialEvent socialEvent( null|string $type )
 * @method static SportingGoodsStore sportingGoodsStore( null|string $type )
 * @method static SportsClub sportsClub( null|string $type )
 * @method static SportsEvent sportsEvent( null|string $type )
 * @method static SportsTeam sportsTeam( null|string $type )
 * @method static StadiumOrArena stadiumOrArena( null|string $type )
 * @method static State state( null|string $type )
 * @method static SubwayStation subwayStation( null|string $type )
 * @method static Synagogue synagogue( null|string $type )
 * @method static Episode episode( null|string $type )
 * @method static TVEpisode tVEpisode( null|string $type )
 * @method static Season season( null|string $type )
 * @method static CreativeWorkSeason creativeWorkSeason( null|string $type )
 * @method static TVSeason tVSeason( null|string $type )
 * @method static Series series( null|string $type )
 * @method static CreativeWorkSeries creativeWorkSeries( null|string $type )
 * @method static TVSeries tVSeries( null|string $type )
 * @method static VideoGameSeries videoGameSeries( null|string $type )
 * @method static Table table( null|string $type )
 * @method static TattooParlor tattooParlor( null|string $type )
 * @method static TaxiStand taxiStand( null|string $type )
 * @method static TelevisionStation televisionStation( null|string $type )
 * @method static TennisComplex tennisComplex( null|string $type )
 * @method static TheaterEvent theaterEvent( null|string $type )
 * @method static TheaterGroup theaterGroup( null|string $type )
 * @method static TireShop tireShop( null|string $type )
 * @method static TouristAttraction touristAttraction( null|string $type )
 * @method static TouristInformationCenter touristInformationCenter( null|string $type )
 * @method static ToyStore toyStore( null|string $type )
 * @method static TrainStation trainStation( null|string $type )
 * @method static TravelAgency travelAgency( null|string $type )
 * @method static UserInteraction userInteraction( null|string $type )
 * @method static UserBlocks userBlocks( null|string $type )
 * @method static UserCheckins userCheckins( null|string $type )
 * @method static UserComments userComments( null|string $type )
 * @method static UserDownloads userDownloads( null|string $type )
 * @method static UserLikes userLikes( null|string $type )
 * @method static UserPageVisits userPageVisits( null|string $type )
 * @method static UserPlays userPlays( null|string $type )
 * @method static UserPlusOnes userPlusOnes( null|string $type )
 * @method static UserTweets userTweets( null|string $type )
 * @method static VideoGallery videoGallery( null|string $type )
 * @method static VideoObject videoObject( null|string $type )
 * @method static VisualArtsEvent visualArtsEvent( null|string $type )
 * @method static ExhibitionEvent exhibitionEvent( null|string $type )
 * @method static Volcano volcano( null|string $type )
 * @method static WPAdBlock wPAdBlock( null|string $type )
 * @method static WPFooter wPFooter( null|string $type )
 * @method static WPHeader wPHeader( null|string $type )
 * @method static WPSideBar wPSideBar( null|string $type )
 * @method static Waterfall waterfall( null|string $type )
 * @method static WebApplication webApplication( null|string $type )
 * @method static WholesaleStore wholesaleStore( null|string $type )
 * @method static Winery winery( null|string $type )
 * @method static Zoo zoo( null|string $type )
 * @method static Brand brand( null|string $type )
 * @method static BusinessEntityType businessEntityType( null|string $type )
 * @method static CreditCard creditCard( null|string $type )
 * @method static BusinessFunction businessFunction( null|string $type )
 * @method static PaymentMethod paymentMethod( null|string $type )
 * @method static DayOfWeek dayOfWeek( null|string $type )
 * @method static PriceSpecification priceSpecification( null|string $type )
 * @method static DeliveryChargeSpecification deliveryChargeSpecification( null|string $type )
 * @method static DeliveryMethod deliveryMethod( null|string $type )
 * @method static Demand demand( null|string $type )
 * @method static IndividualProduct individualProduct( null|string $type )
 * @method static OpeningHoursSpecification openingHoursSpecification( null|string $type )
 * @method static OwnershipInfo ownershipInfo( null|string $type )
 * @method static ParcelService parcelService( null|string $type )
 * @method static PaymentChargeSpecification paymentChargeSpecification( null|string $type )
 * @method static ProductModel productModel( null|string $type )
 * @method static QualitativeValue qualitativeValue( null|string $type )
 * @method static QuantitativeValue quantitativeValue( null|string $type )
 * @method static SomeProducts someProducts( null|string $type )
 * @method static TypeAndQuantityNode typeAndQuantityNode( null|string $type )
 * @method static UnitPriceSpecification unitPriceSpecification( null|string $type )
 * @method static WarrantyPromise warrantyPromise( null|string $type )
 * @method static WarrantyScope warrantyScope( null|string $type )
 * @method static TechArticle techArticle( null|string $type )
 * @method static APIReference aPIReference( null|string $type )
 * @method static Code code( null|string $type )
 * @method static SoftwareSourceCode softwareSourceCode( null|string $type )
 * @method static ParentAudience parentAudience( null|string $type )
 * @method static AlignmentObject alignmentObject( null|string $type )
 * @method static EducationalAudience educationalAudience( null|string $type )
 * @method static DataCatalog dataCatalog( null|string $type )
 * @method static DataDownload dataDownload( null|string $type )
 * @method static Dataset dataset( null|string $type )
 * @method static PublicationEvent publicationEvent( null|string $type )
 * @method static BroadcastEvent broadcastEvent( null|string $type )
 * @method static BroadcastService broadcastService( null|string $type )
 * @method static Clip clip( null|string $type )
 * @method static OnDemandEvent onDemandEvent( null|string $type )
 * @method static RadioClip radioClip( null|string $type )
 * @method static RadioEpisode radioEpisode( null|string $type )
 * @method static RadioSeason radioSeason( null|string $type )
 * @method static RadioSeries radioSeries( null|string $type )
 * @method static TVClip tVClip( null|string $type )
 * @method static BusinessAudience businessAudience( null|string $type )
 * @method static ContactPointOption contactPointOption( null|string $type )
 * @method static Permit permit( null|string $type )
 * @method static GovernmentPermit governmentPermit( null|string $type )
 * @method static Service service( null|string $type )
 * @method static GovernmentService governmentService( null|string $type )
 * @method static ServiceChannel serviceChannel( null|string $type )
 * @method static EventStatusType eventStatusType( null|string $type )
 * @method static DeliveryEvent deliveryEvent( null|string $type )
 * @method static LockerDelivery lockerDelivery( null|string $type )
 * @method static Order order( null|string $type )
 * @method static OrderStatus orderStatus( null|string $type )
 * @method static ParcelDelivery parcelDelivery( null|string $type )
 * @method static OrderItem orderItem( null|string $type )
 * @method static Action action( null|string $type )
 * @method static OrganizeAction organizeAction( null|string $type )
 * @method static AllocateAction allocateAction( null|string $type )
 * @method static AcceptAction acceptAction( null|string $type )
 * @method static AchieveAction achieveAction( null|string $type )
 * @method static UpdateAction updateAction( null|string $type )
 * @method static AddAction addAction( null|string $type )
 * @method static AssessAction assessAction( null|string $type )
 * @method static ReactAction reactAction( null|string $type )
 * @method static AgreeAction agreeAction( null|string $type )
 * @method static InsertAction insertAction( null|string $type )
 * @method static AppendAction appendAction( null|string $type )
 * @method static ApplyAction applyAction( null|string $type )
 * @method static MoveAction moveAction( null|string $type )
 * @method static ArriveAction arriveAction( null|string $type )
 * @method static InteractAction interactAction( null|string $type )
 * @method static CommunicateAction communicateAction( null|string $type )
 * @method static AskAction askAction( null|string $type )
 * @method static AssignAction assignAction( null|string $type )
 * @method static AuthorizeAction authorizeAction( null|string $type )
 * @method static BefriendAction befriendAction( null|string $type )
 * @method static BookmarkAction bookmarkAction( null|string $type )
 * @method static TransferAction transferAction( null|string $type )
 * @method static BorrowAction borrowAction( null|string $type )
 * @method static TradeAction tradeAction( null|string $type )
 * @method static BuyAction buyAction( null|string $type )
 * @method static PlanAction planAction( null|string $type )
 * @method static CancelAction cancelAction( null|string $type )
 * @method static FindAction findAction( null|string $type )
 * @method static CheckAction checkAction( null|string $type )
 * @method static CheckInAction checkInAction( null|string $type )
 * @method static CheckOutAction checkOutAction( null|string $type )
 * @method static ChooseAction chooseAction( null|string $type )
 * @method static CommentAction commentAction( null|string $type )
 * @method static InformAction informAction( null|string $type )
 * @method static ConfirmAction confirmAction( null|string $type )
 * @method static ConsumeAction consumeAction( null|string $type )
 * @method static CreateAction createAction( null|string $type )
 * @method static CookAction cookAction( null|string $type )
 * @method static DeleteAction deleteAction( null|string $type )
 * @method static DepartAction departAction( null|string $type )
 * @method static DisagreeAction disagreeAction( null|string $type )
 * @method static DiscoverAction discoverAction( null|string $type )
 * @method static DislikeAction dislikeAction( null|string $type )
 * @method static DonateAction donateAction( null|string $type )
 * @method static DownloadAction downloadAction( null|string $type )
 * @method static DrawAction drawAction( null|string $type )
 * @method static DrinkAction drinkAction( null|string $type )
 * @method static EatAction eatAction( null|string $type )
 * @method static EndorseAction endorseAction( null|string $type )
 * @method static ControlAction controlAction( null|string $type )
 * @method static ActivateAction activateAction( null|string $type )
 * @method static DeactivateAction deactivateAction( null|string $type )
 * @method static ResumeAction resumeAction( null|string $type )
 * @method static SuspendAction suspendAction( null|string $type )
 * @method static PlayAction playAction( null|string $type )
 * @method static ExerciseAction exerciseAction( null|string $type )
 * @method static FilmAction filmAction( null|string $type )
 * @method static FollowAction followAction( null|string $type )
 * @method static GiveAction giveAction( null|string $type )
 * @method static IgnoreAction ignoreAction( null|string $type )
 * @method static InstallAction installAction( null|string $type )
 * @method static InviteAction inviteAction( null|string $type )
 * @method static JoinAction joinAction( null|string $type )
 * @method static LeaveAction leaveAction( null|string $type )
 * @method static LendAction lendAction( null|string $type )
 * @method static LikeAction likeAction( null|string $type )
 * @method static ListenAction listenAction( null|string $type )
 * @method static LoseAction loseAction( null|string $type )
 * @method static MarryAction marryAction( null|string $type )
 * @method static OrderAction orderAction( null|string $type )
 * @method static PaintAction paintAction( null|string $type )
 * @method static PayAction payAction( null|string $type )
 * @method static PerformAction performAction( null|string $type )
 * @method static PhotographAction photographAction( null|string $type )
 * @method static PrependAction prependAction( null|string $type )
 * @method static QuoteAction quoteAction( null|string $type )
 * @method static ReadAction readAction( null|string $type )
 * @method static ReceiveAction receiveAction( null|string $type )
 * @method static RegisterAction registerAction( null|string $type )
 * @method static RejectAction rejectAction( null|string $type )
 * @method static RentAction rentAction( null|string $type )
 * @method static ReplaceAction replaceAction( null|string $type )
 * @method static ReplyAction replyAction( null|string $type )
 * @method static ReserveAction reserveAction( null|string $type )
 * @method static ReturnAction returnAction( null|string $type )
 * @method static ReviewAction reviewAction( null|string $type )
 * @method static RsvpAction rsvpAction( null|string $type )
 * @method static RsvpResponseType rsvpResponseType( null|string $type )
 * @method static ScheduleAction scheduleAction( null|string $type )
 * @method static SearchAction searchAction( null|string $type )
 * @method static SellAction sellAction( null|string $type )
 * @method static SendAction sendAction( null|string $type )
 * @method static ShareAction shareAction( null|string $type )
 * @method static SubscribeAction subscribeAction( null|string $type )
 * @method static TakeAction takeAction( null|string $type )
 * @method static TieAction tieAction( null|string $type )
 * @method static TipAction tipAction( null|string $type )
 * @method static TrackAction trackAction( null|string $type )
 * @method static TravelAction travelAction( null|string $type )
 * @method static UnRegisterAction unRegisterAction( null|string $type )
 * @method static UseAction useAction( null|string $type )
 * @method static ViewAction viewAction( null|string $type )
 * @method static VoteAction voteAction( null|string $type )
 * @method static WantAction wantAction( null|string $type )
 * @method static WatchAction watchAction( null|string $type )
 * @method static WearAction wearAction( null|string $type )
 * @method static WinAction winAction( null|string $type )
 * @method static WriteAction writeAction( null|string $type )
 * @method static GenderType genderType( null|string $type )
 * @method static ItemListOrderType itemListOrderType( null|string $type )
 * @method static Reservation reservation( null|string $type )
 * @method static BusReservation busReservation( null|string $type )
 * @method static EventReservation eventReservation( null|string $type )
 * @method static FlightReservation flightReservation( null|string $type )
 * @method static FoodEstablishmentReservation foodEstablishmentReservation( null|string $type )
 * @method static LodgingReservation lodgingReservation( null|string $type )
 * @method static RentalCarReservation rentalCarReservation( null|string $type )
 * @method static TaxiReservation taxiReservation( null|string $type )
 * @method static TrainReservation trainReservation( null|string $type )
 * @method static ReservationPackage reservationPackage( null|string $type )
 * @method static ReservationStatusType reservationStatusType( null|string $type )
 * @method static BusTrip busTrip( null|string $type )
 * @method static TrainTrip trainTrip( null|string $type )
 * @method static Flight flight( null|string $type )
 * @method static Airline airline( null|string $type )
 * @method static ProgramMembership programMembership( null|string $type )
 * @method static Ticket ticket( null|string $type )
 * @method static Seat seat( null|string $type )
 * @method static Taxi taxi( null|string $type )
 * @method static TaxiService taxiService( null|string $type )
 * @method static Vehicle vehicle( null|string $type )
 * @method static Car car( null|string $type )
 * @method static BoardingPolicyType boardingPolicyType( null|string $type )
 * @method static QAPage qAPage( null|string $type )
 * @method static Question question( null|string $type )
 * @method static Answer answer( null|string $type )
 * @method static EmailMessage emailMessage( null|string $type )
 * @method static ActionStatusType actionStatusType( null|string $type )
 * @method static EntryPoint entryPoint( null|string $type )
 * @method static PropertyValueSpecification propertyValueSpecification( null|string $type )
 * @method static Role role( null|string $type )
 * @method static PerformanceRole performanceRole( null|string $type )
 * @method static OrganizationRole organizationRole( null|string $type )
 * @method static EmployeeRole employeeRole( null|string $type )
 * @method static WebSite webSite( null|string $type )
 * @method static Periodical periodical( null|string $type )
 * @method static PublicationVolume publicationVolume( null|string $type )
 * @method static PublicationIssue publicationIssue( null|string $type )
 * @method static ListItem listItem( null|string $type )
 * @method static MovieSeries movieSeries( null|string $type )
 * @method static BookSeries bookSeries( null|string $type )
 * @method static VideoGame videoGame( null|string $type )
 * @method static VideoGameClip videoGameClip( null|string $type )
 * @method static GameServer gameServer( null|string $type )
 * @method static Game game( null|string $type )
 * @method static GamePlayMode gamePlayMode( null|string $type )
 * @method static GameServerStatus gameServerStatus( null|string $type )
 * @method static MusicAlbumProductionType musicAlbumProductionType( null|string $type )
 * @method static MusicAlbumReleaseType musicAlbumReleaseType( null|string $type )
 * @method static MusicComposition musicComposition( null|string $type )
 * @method static MusicRelease musicRelease( null|string $type )
 * @method static MusicReleaseFormatType musicReleaseFormatType( null|string $type )
 * @method static SportsOrganization sportsOrganization( null|string $type )
 * @method static DatedMoneySpecification datedMoneySpecification( null|string $type )
 * @method static VisualArtwork visualArtwork( null|string $type )
 * @method static Invoice invoice( null|string $type )
 * @method static PropertyValue propertyValue( null|string $type )
 * @method static CableOrSatelliteService cableOrSatelliteService( null|string $type )
 * @method static BroadcastChannel broadcastChannel( null|string $type )
 * @method static TelevisionChannel televisionChannel( null|string $type )
 * @method static RadioChannel radioChannel( null|string $type )
 * @method static EngineSpecification engineSpecification( null|string $type )
 * @method static Bridge bridge( null|string $type )
 * @method static DriveWheelConfigurationValue driveWheelConfigurationValue( null|string $type )
 * @method static SteeringPositionValue steeringPositionValue( null|string $type )
 * @method static InteractionCounter interactionCounter( null|string $type )
 * @method static SocialMediaPosting socialMediaPosting( null|string $type )
 * @method static DiscussionForumPosting discussionForumPosting( null|string $type )
 * @method static LiveBlogPosting liveBlogPosting( null|string $type )
 * @method static PaymentStatusType paymentStatusType( null|string $type )
 * @method static Report report( null|string $type )
 * @method static Enumeration enumeration( null|string $type )
 * @method static NutritionInformation nutritionInformation( null|string $type )
 * @method static Recipe recipe( null|string $type )
 * @method static DataFeed dataFeed( null|string $type )
 * @method static DataFeedItem dataFeedItem( null|string $type )
 * @method static CompoundPriceSpecification compoundPriceSpecification( null|string $type )
 * @method static MonetaryAmount monetaryAmount( null|string $type )
 * @method static FinancialProduct financialProduct( null|string $type )
 * @method static BankAccount bankAccount( null|string $type )
 * @method static DepositAccount depositAccount( null|string $type )
 * @method static LoanOrCredit loanOrCredit( null|string $type )
 * @method static PaymentCard paymentCard( null|string $type )
 * @method static InvestmentOrDeposit investmentOrDeposit( null|string $type )
 * @method static PaymentService paymentService( null|string $type )
 * @method static CurrencyConversionService currencyConversionService( null|string $type )
 * @method static Conversation conversation( null|string $type )
 * @method static Message message( null|string $type )
 * @method static DigitalDocument digitalDocument( null|string $type )
 * @method static PresentationDigitalDocument presentationDigitalDocument( null|string $type )
 * @method static SpreadsheetDigitalDocument spreadsheetDigitalDocument( null|string $type )
 * @method static TextDigitalDocument textDigitalDocument( null|string $type )
 * @method static NoteDigitalDocument noteDigitalDocument( null|string $type )
 * @method static DigitalDocumentPermission digitalDocumentPermission( null|string $type )
 * @method static DigitalDocumentPermissionType digitalDocumentPermissionType( null|string $type )
 * @method static Resort resort( null|string $type )
 * @method static Accommodation accommodation( null|string $type )
 * @method static Room room( null|string $type )
 * @method static HotelRoom hotelRoom( null|string $type )
 * @method static MeetingRoom meetingRoom( null|string $type )
 * @method static CampingPitch campingPitch( null|string $type )
 * @method static Suite suite( null|string $type )
 * @method static House house( null|string $type )
 * @method static Apartment apartment( null|string $type )
 * @method static FoodService foodService( null|string $type )
 * @method static LocationFeatureSpecification locationFeatureSpecification( null|string $type )
 * @method static BedDetails bedDetails( null|string $type )
 * @method static Course course( null|string $type )
 * @method static CourseInstance courseInstance( null|string $type )
 * @method static ClaimReview claimReview( null|string $type )
 * @method static Menu menu( null|string $type )
 * @method static MenuItem menuItem( null|string $type )
 * @method static MenuSection menuSection( null|string $type )
 * @method static Unknown unknown( null|string $type )
 */
class Schema
{
    public static function __callStatic( $name, $arguments )
    {
        $className = sprintf( '%s\%s', __NAMESPACE__, ucfirst( $name ));
        $type = isset( $arguments[0] ) ? $arguments[0] : null;
        return class_exists( $className )
            ? new $className()
            : new Unknown( $type );
    }
}