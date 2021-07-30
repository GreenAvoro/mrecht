jQuery(document).ready(function ($) {
	"use strict";

	$('#input-country').select2()
	$('#input-state').select2()

	$(".owl-carousel").owlCarousel({
		loop:true,
		responsive:{
			0:{
				items:2
			},
			600:{
				items:3
			},
			1000:{
				items:6
			}
		},
		nav: true
	});
	$("#scrollBtn").click(function () {

		$("html, body").animate({

			scrollTop: 0

		}, 500);

	});

	$('.contact-item a').mouseover(function(){
		//Mouse In
		$('.contact-dropdown').slideDown('fast')
	})
	$('.contact-dropdown').mouseleave(function(){
		//Mouse In
		$('.contact-dropdown').slideUp('fast')
	})

	$(".search-form").click(function () {

		if ($('.search-icon').hasClass('fa-search')) {
			$('.nav-form-content').addClass('show');
			$('.search-icon').removeClass('fa-search');
			$('.search-icon').addClass('fa-times');
		} else {
			$('.nav-form-content').removeClass('show');
			$('.search-icon').removeClass('fa-times');
			$('.search-icon').addClass('fa-search');
		}

	});

	function scrollFunction() {

		if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {

			document.getElementById("scrollBtn").style.display = "block";

			$('.desktopnav').removeClass('top');

			$('.desktopnav').addClass('scroll');

		} else {

			document.getElementById("scrollBtn").style.display = "none";

			$('.desktopnav').removeClass('scroll');

			$('.desktopnav').addClass('top');

		}

	}

	window.onscroll = function () {
		scrollFunction();
	};

	$(".mobile-menu-screen--tabs .screen-tab").click(function () {
		const nextScreen = $(this).children("a").data("link");

		if (nextScreen) {
			$(".mobile-menu-screen").removeClass("active");
			$("#" + nextScreen).addClass("active");
		} else {
			const dropdown = $(this).children(".screen-dropdown");

			if (dropdown.hasClass("active")) {
				dropdown.removeClass("active");
				dropdown.parent().removeClass("active");
			} else {
				$(".mobile-menu-screen--tabs .screen-tab .screen-dropdown").removeClass("active");
				$(".mobile-menu-screen--tabs .screen-tab.expandable").removeClass("active");
				dropdown.addClass("active");
				dropdown.parent().addClass("active");
			}

		}
	})

	$(".mobile-menu-screen--top-bar").click(function () {
		const nextScreen = $(this).children("a").data("link");

		$(".mobile-menu-screen").removeClass("active");
		$("#" + nextScreen).addClass("active");
	})

	$("#mobile-search-icon").click(function () {
		const mobileSearchBar = $(".mobile--search-bar");

		closeMenu();

		if (mobileSearchBar.hasClass("active")) {
			mobileSearchBar.removeClass("active");
		} else {
			mobileSearchBar.addClass("active");
		}
	});

	$('.testimonials-carousel .owl-carousel').owlCarousel({
		margin: 10,
		nav: true,
		dots: false,
		navText: [
			'<i class="fa fa-angle-left" aria-hidden="true"></i>',
			'<i class="fa fa-angle-right" aria-hidden="true"></i>'
		],
		items: 1
	});


	$(".footer-nav.mobile .menu-item").click(function(){
		$(this).find('.dropdown').toggle(100, 'swing');
	})
	let timer = setInterval(() => changeBanner($), 15000)



	// CONTROL THE MAIN DROPDOWN MENU
	let shopDown = false
	let servicesDown = false
	let resourcesDown = false

	$('.menu-item').mouseover(function(){
		
		switch($(this).find('a').text()){
			case "ACCESSORIES":
				if(shopDown) return;
				$('.main-dropdown.shop').slideDown({
					'duration': 'fast',
					'start': () => $('.main-dropdown.shop').css('display', 'grid')
				})
				$('.main-dropdown.services').css('display', 'none');
				$('.main-dropdown.resources').css('display', 'none');
				shopDown = true
				servicesDown = false
				resourcesDown = false
				break
			case "SERVICES":
				if(servicesDown) return;
				$('.main-dropdown.services').slideDown('fast')
				$('.main-dropdown.shop').css('display', 'none');
				$('.main-dropdown.resources').css('display', 'none');
				shopDown = false
				servicesDown = true
				resourcesDown = false
				break
			case "RESOURCES":
				if(resourcesDown) return;
				$('.main-dropdown.resources').slideDown('fast')
				$('.main-dropdown.services').css('display', 'none');
				$('.main-dropdown.shop').css('display', 'none');
				shopDown = false
				servicesDown = false
				resourcesDown = true
				break
			default:
				break
		}
	})
	$(".my-close").click(function(){
		$('.main-dropdown').slideUp({
			'duration': 'fast',
			'start': () => $('.main-dropdown').css('display', 'none')
		});
		shopDown = false
		servicesDown = false
		resourcesDown = false
	})




	$(".left-panel-text a").mouseover(function(){
		let idToDisplay = $(this).data('id')
		console.log(idToDisplay);
		$(`.menu-subcategory-grid`).css("display", "none")
		$(`.menu-subcategory-grid[data-id="${idToDisplay}"]`).css("display", "flex")
	});


	//Extra product info on single product page
	//Controls the tabs interface
	$(".header-tab").click(function(){
		$(".header-tab").removeClass('active')
		$(this).addClass("active")
		$(".single-product-info-tab").css("display", "none");
		for(let i=1;i <= 3;i++){
			if($(this).hasClass(i.toString())){
				console.log(i)
				$(`.single-product-info-tab.tab${i}`).css("display", "block");
			}
		}
	})
	$(".slide-out-list-items .item").click(function(){
		let clicked = $(this)
		if(clicked.data('haschild')){
			$(".back-button").removeClass("hide")
			$(".slide-out-list-items-header").removeClass("hide")
			$(".slide-out-list-items-header").text(clicked.data("name"))
			$(".back-button").data("parent", clicked.data("parent"))
			$(".slide-out-list-items .item").each(function(){
				if($(this).data('parent') !== clicked.data("id")){
					$(this).addClass("hide")
				}else{
					$(this).removeClass("hide")
					$(this).css("paddingLeft", "3em")
					$(this).animate({
						paddingLeft: "1em"
					},200,"linear")
					
				}
			})
		}
	})
	$(".back-button").click(function(){
		let parent = $(this).data("parent")
		$(".slide-out-list-items .item").each(function(){
			if($(this).data('parent') !== parent){
				$(this).addClass("hide")
			}else{
				$(this).removeClass("hide")
				$(this).css("paddingLeft", "3em")
				$(this).animate({
					paddingLeft: "1em"
				},200,"linear")
				
			}
		})
		if(parent === 0){
			$(this).addClass("hide")
			$(".slide-out-list-items-header").addClass("hide")
		}
	})
	$("#menu-close i").click(function(){
		$(".slide-out-menu-2").removeClass("show-menu")
		$("body").removeClass("no-scroll")
	})
	$(".filter-row input").click(function(e){
		e.stopPropagation()
	})
	$(".filter-row").click(function(){
		$(this).toggleClass('highlight')
		$(this).find(".filter-row-subcategories").toggle()
		$(this).find(".main-cat .fa-plus").toggle()
		$(this).find(".main-cat .fa-minus").toggle()
		$(this).find(".main-cat p").toggleClass("bold")

	})
	$("#reset-filter").click(function(){
		$(".filter-row input").prop('checked', false)
	})

	$(".show-filter-button").click(function(){
		$('.filter-container').addClass('show')
		$('body').addClass('no-scroll')
	})
	$(".close-filter").click(function(){
		$('.filter-container').removeClass('show')
		$('body').removeClass('no-scroll')
	})

	$("#input-account-type option").click(function(){
		if($(this).val() === 'schl'){
			$('.school-inputs').removeClass("hide")
			$('.other-inputs').addClass("hide")
		}else{
			$('.school-inputs').addClass("hide")
			$('.other-inputs').removeClass("hide")
		}
	})
	$("#input-account-type option").click(function(){
		if($(this).val() === 'schl'){
			$('.school-inputs').removeClass("hide")
			$('.trade-inputs').addClass("hide")
			$('.other-inputs').addClass("hide")
		}
		else if($(this).val() === 'trade'){
			$('.school-inputs').addClass("hide")
			$('.trade-inputs').removeClass("hide")
			$('.other-inputs').addClass("hide")
		}else{
			$('.school-inputs').addClass("hide")
			$('.trade-inputs').addClass("hide")
			$('.other-inputs').removeClass("hide")
		}
	})
	$("#input-school option").click(function(){
		$("#input-school-id").val($(this).val())
	})

});


const mobileMenu = document.getElementsByClassName("slide-out-menu-2")[0];
// const menuContainer = document.getElementsByClassName("slide-out-menu--container")[0];
const menuOpenIcon = document.getElementById("menu-icon");
const menuCloseIcon = document.getElementById("menu-close");

//Mobile menu slide out
menuOpenIcon.addEventListener("click", function () {
	openMenu();
});

//Mobile menu slide in
menuCloseIcon.addEventListener("click", function () {
	console.log("RAWR")
	closeMenu();
});

// mobileMenu.addEventListener("click", function () {
//  closeMenu()
// });

function openMenu() {
	mobileMenu.classList.add("show-menu");
	document.body.classList.add("no-scroll")
	// menuContainer.classList.add("active");
}

function closeMenu() {
	mobileMenu.classList.remove("show-menu");
	// menuContainer.classList.remove("active");
}