<!-- ===== Call to Action start ===== -->

  <div class="ud-container">
    <div
      class="
        ud-bg-gradient-to-l ud-from-gradient-1
        dark:ud-from-[#3c3e56] dark:ud-to-black
        ud-rounded-[20px] ud-px-7
        sm:ud-px-10
        md:ud-px-16
        lg:ud-px-14
        xl:ud-px-16
        wow
        fadeInUp
      "
      data-wow-delay=".2s"
    >
      <div class="ud-flex ud-flex-wrap ud-items-end ud--mx-4">
        <div class="ud-w-full lg:ud-w-1/2 ud-px-4">
          <div class="ud-max-w-[400px] ud-py-16">
            <span
              class="
                ud-font-bold ud-text-base ud-text-primary ud-block ud-mb-2
              "
            >
              {{__('Get Access')}}
            </span>
            <h2
              class="
                ud-font-extrabold ud-text-3xl
                sm:ud-text-4xl
                ud-leading-tight ud-text-black
                dark:text-white
                ud-mb-7
              "
            >
              {{__('Open a Free Account')}}
            </h2>
            <p
              class="
                ud-text-base
                ud-leading-relaxed
                ud-text-body-color
                ud-mb-10
              "
            >
              {{__("The basic version is FREE and always will be. Sign up and get access to the :appname now.",["appname"=>config("app.name")])}}
            </p>
            <div class="ud-flex ud-items-center">
              <a
                href="{{route('register')}}"
                class="
                  ud-flex
                  ud-items-center
                  ud-bg-primary
                  ud-rounded-xl
                  ud-py-3
                  ud-px-3
                  sm:ud-px-4
                  ud-transition-all
                  hover:ud-shadow-primary-hover
                  ud-mr-2
                  sm:ud-mr-5
                "
              >
                <span class="ud-pr-3">
                  <i class="fas fa-user-circle text-white ud-text-4xl"></i>
                </span>
                <span class="ud-font-bold ud-text-white ud-text-lg">
                  <span
                    class="ud-block ud-text-xs ud-text-white ud-opacity-70"
                  >
                    {{__('Open a')}}
                  </span>
                 {{__('FREE Acoount')}}
                </span>
              </a>
            </div>
          </div>
        </div>
        <div class="ud-w-full lg:ud-w-1/2 ud-px-4">
          <div
            class="
              ud-relative ud-w-full ud-flex ud-justify-end ud-items-end
            "
          >
            <div class="ud-w-full">
              <img
                src="{{$get_landing_language->details_feature_8_img ?? ''}}"
                alt="image"
                class="ud-relative ud-z-10 ud-drop-shadow-image"
              />
            </div>
            <div class="ud-w-full ud--ml-8">
              <img
                src="{{$get_landing_language->details_feature_9_img ?? ''}}"
                alt="image"
                class="ud-relative ud-z-0 ud-drop-shadow-image"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- ===== Call to Action end ===== -->