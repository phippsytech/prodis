<script>

// src: https://codepen.io/chrisgannon/pen/yLQyeEy    


gsap.config({trialWarn: false});
let select = s => document.querySelector(s),
		mainSVG = select('#mainSVG'),
		dialToothContainer = select('#dialToothContainer'),
		dialBodyShine = select('#dialBodyShine'),
		dialMarker = select('#dialMarker'),
		dialTooth = select('.dialTooth'),
		numTeeth = 32,
		dialToothArray = [],
		maxRotation = 270,
		style = getComputedStyle(document.body),
		uiGrey = style.getPropertyValue('--ui-grey'),
		dragger = null,
		currentTooth = 0,
		toothRadius = 4,
		dialColorArray = [style.getPropertyValue('--ui-green'),style.getPropertyValue('--ui-amber'),style.getPropertyValue('--ui-red')],
		step = maxRotation/(numTeeth-1),
		dialColor = gsap.utils.interpolate(dialColorArray),
		toothIdClamp = gsap.utils.clamp(0, numTeeth-1),
		jumpEase = CustomEase.create("custom", "M0,0 C0.25,0 0.342,0.22 0.384,0.422 0.474,0.864 0.698,1 1,1 ")

gsap.set('svg', {
	visibility: 'visible'
})
function blendEases(startEase, endEase, blender) {
	var parse = function(ease) {
					return typeof(ease) === "function" ? ease : gsap.parseEase("power4.inOut");
			},
			s = gsap.parseEase(startEase),
			e = gsap.parseEase(endEase),
			blender = parse(blender);
	return function(v) {
		var b = blender(v);
		return s(v) * (1 - b) + e(v) * b;
	};
}
gsap.set(dialMarker, {
	x: 334,
	y: 356
})


function jumpTo(toothId, duration) {	
	toothId = toothIdClamp(toothId);	
	gsap.to(dialMarker, {
		rotation: toothId * step,
		onUpdate: onDrag,
		duration: duration || 0.5,
		ease: jumpEase
	})
}
gsap.set(dialMarker, {
	svgOrigin: `${dialBodyShine.getAttribute('cx')} ${dialBodyShine.getAttribute('cy')}`,
	attr: {
		r: toothRadius * 2
	}
})
for(let i = 0; i < numTeeth; i++) {
	let clone = dialTooth.cloneNode(true);
	dialToothContainer.appendChild(clone);	
	clone.addEventListener('click', function(e) {
		jumpTo(i)
	})
	gsap.set(clone, {
		rotation: i * step,
		svgOrigin: `${dialBodyShine.getAttribute('cx')} ${dialBodyShine.getAttribute('cy')}`,
		attr: {
			r: toothRadius
		}
	})
	gsap.from(clone, {
		attr: {
			r: 10
		},
		opacity: 0,
		delay: i/numTeeth
	})
	dialToothArray.push(clone)
}
let pipeRotation = gsap.utils.pipe(
	gsap.utils.mapRange(0, 1, 1, dialToothArray.length),
	gsap.utils.snap(1)
)



function onDrag () {
	let dialRotation = gsap.getProperty(dialMarker, 'rotation');
	let rotationProgress = dialRotation/maxRotation;
	currentTooth = pipeRotation(rotationProgress);
	//tooth colours
	gsap.to(dialToothArray, {
		fill: (i) => currentTooth <= i ? uiGrey : dialColor(i/(dialToothArray.length)),
		duration: 0.25
	})	
	//dial marker colour
	gsap.set(dialMarker, {
		fill: (i) => dialColor(currentTooth/(dialToothArray.length))
	})
	//want the shadow to follow the current color? No problem!
/* 	gsap.set(':root', {
		'--ui-shadow': dialColor(currentTooth/(dialToothArray.length))
	})	 
	
	*/

}
function onDial(str) {
	
	gsap.to('#dialBodyGroup', {
		scale: str == 'press' ? 0.96 : 1,
		duration: 0.6,
		ease:str == 'press' ? 'expo' : 'elastic(0.5, 0.25)'
	})
}
dragger = Draggable.create(dialMarker, {
	type: 'rotation',
	trigger: '#dialBodyGroup',
	bounds: {min: 0, max: maxRotation},
	onDrag: onDrag,
	onThrowUpdate: onDrag,
	inertia: true,
	dragResistance: 0.23,
	onPress: onDial, 
	onPressParams: ['press'],
	onRelease: onDial,
	onReleaseParams: ['release'],
	overshootTolerance: 0.3,
	throwResistance : 3000
})
let initTl = gsap.timeline();
initTl.from('#dialBodyGroup', {
	scale: 0,
	transformOrigin: '50% 50%',
	onComplete: function() {
		jumpTo(28, 1.5)
	},
	ease: blendEases('power1.in','expo'),
	duration: 1.6	
})



</script>

<style>

body {
 background-color: #dfdfdf;
 overflow: hidden;
 text-align:center;
  display: flex;
  align-items: center;
  justify-content: center; 
}
:root {
	--ui-grey: #d9d9d9;
	--ui-green: #1DD1A1;
	--ui-amber: #FECA57;
	--ui-red: #FF6B6B;
	--ui-shadow: #697683;
	--ui-shadow-glow: #1DD1A1;
}
body,
html {
 height: 100%;
 width: 100%;
 margin: 0;
 padding: 0;
}

svg {
 width: 100%;
 height: 100%;
 visibility: hidden;
 
}
.dialTooth {
	cursor: pointer;
}
#robotArm, #robotMaskShine{ 
pointer-events: none;
}

</style>


<svg id="mainSVG" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">

	<defs>
    <radialGradient id="robotMaskGrad" cx="400" cy="300" fx="400" fy="277.61" r="130.72" gradientUnits="userSpaceOnUse">
      <stop offset=".88" stop-color="#f0f0f0"/>
     
      <stop offset=".99" stop-color="#000"/>
    </radialGradient>
		<circle class="dialTooth" cx="277" cy="419" r="4" fill="var(--ui-grey)" />
<!-- 		<filter id="glow" x="-100%" y="-100%" width="250%" height="250%">
			<feGaussianBlur stdDeviation="24" result="coloredBlur" />
			<feOffset dx="0" dy="-20" result="offsetblur"></feOffset>
			<feFlood id="glowAlpha" flood-color="var(--ui-shadow-glow)" flood-opacity="0.23"></feFlood>
			<feComposite in2="offsetblur" operator="in"></feComposite>
			<feMerge>
				<feMergeNode />
				<feMergeNode in="SourceGraphic"></feMergeNode>
			</feMerge>
		</filter>	 -->	
		<filter id="dropShadow" x="-100%" y="-100%" width="250%" height="250%">
			<feGaussianBlur stdDeviation="12 16" result="coloredBlur" />
			<feOffset dx="0" dy="20" result="offsetblur"></feOffset>
			<feFlood id="glowAlpha" flood-color="var(--ui-shadow)" flood-opacity="0.18"></feFlood>
			<feComposite in2="offsetblur" operator="in"></feComposite>
			<feMerge>
				<feMergeNode />
				<feMergeNode in="SourceGraphic"></feMergeNode>
			</feMerge>
		</filter>
		<radialGradient id="dialBody" cx="400" cy="316" fx="400" fy="316" r="145.43" gradientUnits="userSpaceOnUse">
			<stop offset=".78" stop-color="#f1f1f1" />
			<stop offset="0.95" stop-color="#fff" />
		</radialGradient>
		<radialGradient id="dialBody-2" cx="400" cy="279" fx="400" fy="279" r="145.43" gradientUnits="userSpaceOnUse">
			<stop offset=".78" stop-color="#f0f0f0" stop-opacity="0" />

			<stop offset=".96" stop-color="#e5e5e5" />
		</radialGradient>
		<filter id='innerShadow'>
			<!-- Shadow offset -->
			<feOffset dx='0' dy='1' />

			<!-- Shadow blur -->
			<feGaussianBlur stdDeviation='1' result='offset-blur' />

			<!-- Invert drop shadow to make an inset shadow -->
			<feComposite operator='out' in='SourceGraphic' in2='offset-blur' result='inverse' />

			<!-- Cut color inside shadow -->
			<feFlood flood-color='black' flood-opacity='0.15' result='color' />
			<feComposite operator='in' in='color' in2='inverse' result='shadow' />

			<!-- Placing shadow over element -->
			<feComposite operator='over' in='shadow' in2='SourceGraphic' />
		</filter>
	</defs>

	<g filter="url(#dropShadow)">
		<g id="dialBodyGroup">
			<circle id="dialBodyShine" cx="400" cy="300" r="130.76" fill="url(#dialBody)" />
			<circle id="dialBodyShade" cx="400" cy="300" r="130.76" fill="url(#dialBody-2)" />
			<g filter="url(#innerShadow)">
				<circle id="dialMarker" cx="0" cy="0" r="8" fill="var(--ui-green)" />
			</g>
		</g>
	</g>

	<g filter="url(#innerShadow)" id="dialToothContainer">
		
	</g>
</svg>