<?php

function shortcode_3Displayer($atts)
{
  extract(shortcode_atts(
    array(
      'light_power' => 0,
      'model_size' => 0,
      'model_name' => '',
      'model_speed' => 0,
      'model_scale' => 1,
      'fullscreen_color' => 'white'
    ),
    $atts
  ));

  return '<body onload="createDisplayer()" class="body_displayer">
    <section class="flex">
      <div id="modelDisplayer">
        <div class="displayer-controls">
          <button
            onclick="toggleAnimation()"
            id="displayer-controls-animation"
            title="Prenez le controle du modèle"
          >
            <i class="far fa-hand-paper"></i>
          </button>
          <button
            onclick="toggleFullScreen()"
            id="displayer-controls-screen"
            title="Mettre en plein écran"
          >
            <i class="fas fa-arrows-alt"></i>
          </button>
        </div>
      </div>
    </section>

    <style>
      .flex * {
        box-sizing: border-box;
        outline: none;
      }
      .body_displayer {
        padding: 0;
        margin: 0;
        background-color: lightgray;
      }
      .flex {
        position: relative;
        display: flex;
        justify-content: center;
        margin: auto;
        width: 100%;
      }
      #modelDisplayer {
        width: 100%;
        padding: 0;
        margin: auto;
        position: relative;
        display: flex;
        justify-content: center;
      }
      .displayer-controls {
        position: absolute;
        display: flex;
        justify-content: center;
        left: 0;
        bottom: -4px;
        padding: 0.5rem;
        z-index: 10;
        width: 100%;
      }
      .displayer-controls button {
        background-color: transparent;
        color: black;
        border: none;
        font-size: 1.5em;
      }
    </style>
    <script type="module">
      import * as THREE from "https://cdn.jsdelivr.net/npm/three@0.117.1/build/three.min.js";
      import * as OrbitControls from "https://cdn.jsdelivr.net/npm/three@0.117.1/examples/js/controls/OrbitControls.js";
      import * as GLTFLoader from "https://cdn.jsdelivr.net/npm/three@0.117.1/examples/js/loaders/GLTFLoader.js";
    </script>
    <script
      src="https://kit.fontawesome.com/cbcf9fa854.js"
      crossorigin="anonymous"
    ></script>
    <script>
      var camera,
        scene,
        renderer,
        cube,
        controls,
        el,
        raycaster,
        mouse,
        composer,
        renderPass,
        outlinePass,
        copyPass;
      var cubeRotationX = 0.01;
      var cubeRotationY = 0.01;
      var orbitControlModel = false;
      var lightPower = ' . $light_power . ';
      var modelSize = ' . $model_size . ';
      var models = [
        { name: "' . $model_name . '", speed: ' . $model_speed . ', scale: ' . $model_scale . ' },
      ];

      function onWindowResize() {
        if (document.fullscreen && renderer.getSize().x !== el.offsetWidth) {
          renderer.setSize(screen.height, screen.height);
          el.style.width = screen.height;
          el.style.height = screen.height;
          camera.updateProjectionMatrix();
        } else if (!document.fullscreen) {
          renderer.setSize(el.offsetWidth, el.offsetWidth);
        }
      }

      function onDocumentMouseMove(event) {
        event.preventDefault();

        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
      }

      function createDisplayer() {
        // Find container
        el = document.getElementById("modelDisplayer");

        // Init camera
        // camera = new THREE.PerspectiveCamera(35, 1, 1, 10000);
        camera = new THREE.OrthographicCamera(
          el.offsetWidth / -2,
          el.offsetWidth / 2,
          el.offsetWidth / 2,
          el.offsetWidth / -2,
          1,
          10000
        );

        // Init scene
        scene = new THREE.Scene();

        scene.add(camera);
        // Set renderer
        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(el.offsetWidth, el.offsetWidth);
        renderer.setClearColor(0xffffff, 0);

        // Setup ambiant light
        let light = new THREE.AmbientLight(0xffffff);
        light.intensity = lightPower;
        light.castShadow = false; // soft white light
        scene.add(light);

        // Append to container
        el.appendChild(renderer.domElement);

        // Camera position
        if (modelSize !== 0) {
          camera.position.set(20 * modelSize, 10 * modelSize, 15 * modelSize);
        } else {
          camera.position.set(20, 10, 15);
        }
        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.minZoom = 0.5;
        controls.maxZoom = 7;

        window.addEventListener("resize", onWindowResize, false);

        // Start loader of model
        let loader = new THREE.GLTFLoader();

        models.map((i) => {
          loader.load(
            // resource URL
            i.name,
            // called when the resource is loaded
            function (gltf) {
              gltf.scene.scale.set(i.scale, i.scale, i.scale);
              scene.add(gltf.scene);

              camera.lookAt(gltf.scene.position);
              camera.updateProjectionMatrix();
              function animate() {
                requestAnimationFrame(animate);
                if (!document.fullscreen) {
                  renderer.setSize(el.offsetWidth, el.offsetWidth);
                }

                if (!orbitControlModel) {
                  gltf.scene.rotation.y += 0.003 * i.speed;
                }
                controls.enabled = orbitControlModel;
                renderer.render(scene, camera);
              }

              animate();
            },
            // called while loading is progressing
            function (xhr) {
              xhr.loaded / xhr.total === 1 &&
                console.log("All models are loaded");
            },
            // called when loading has errors
            function (error) {
              console.log("' . $modelName . '");
              console.log(error);
              console.log("An error happened");
            }
          );
        });
        // Load a glTF resource
      }

      // Loop function for update, animation and render

      function toggleAnimation() {
        if (orbitControlModel) {
          orbitControlModel = false;
          document.getElementById("displayer-controls-animation").innerHTML = `
          <i class="far fa-hand-paper"></i>`;
          document.getElementById("displayer-controls-animation").title =
            "Prenez le controle du modèle";
        } else {
          orbitControlModel = true;
          document.getElementById("displayer-controls-animation").innerHTML = `
          <i class="far fa-hand-rock"></i>`;
          document.getElementById("displayer-controls-animation").title =
            "Relacher le modèle";
        }
      }
      function toggleFullScreen() {
        if (!document.fullscreen) {
          let elem = el.parentNode;
          if (elem.requestFullscreen) {
            elem.requestFullscreen();
          } else if (elem.mozRequestFullScreen) {
            /* Firefox */
            elem.mozRequestFullScreen();
          } else if (elem.webkitRequestFullscreen) {
            /* Chrome, Safari and Opera */
            elem.webkitRequestFullscreen();
          } else if (elem.msRequestFullscreen) {
            /* IE/Edge */
            elem.msRequestFullscreen();
          }
        } else {
          
          if (document.exitFullscreen) {
            document.exitFullscreen();
          } else if (document.mozCancelFullScreen) {
            /* Firefox */
            document.mozCancelFullScreen();
          } else if (document.webkitExitFullscreen) {
            /* Chrome, Safari and Opera */
            document.webkitExitFullscreen();
          } else if (document.msExitFullscreen) {
            /* IE/Edge */
            document.msExitFullscreen();
          }
        }
      }
      document.addEventListener("fullscreenchange", function( event ) {
        if ( !document.fullscreen ) {
          renderer.setClearColor(0xffffff, 0);
          document.getElementById("displayer-controls-screen").innerHTML = `
          <i class="fas fa-arrows-alt"></i>`;
        } else {
          renderer.setClearColor("' . $fullscreen_color . '");
          document.getElementById("displayer-controls-screen").innerHTML = `
          <i class="fas fa-compress-arrows-alt"></i>`;
        }
    });
    </script>
  </body>';
}
add_shortcode('3Displayer', 'shortcode_3Displayer');
