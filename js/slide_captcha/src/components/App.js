import React, { Component } from "react";
import axios from "axios";
import '../styles/App.css';

class App extends Component {

	validateStatus = {
		init: 0,
		success: 1,
		error: - 1
	};

	imgDisplayStatus = {
		show:  'block',
		hidden: 'none'
	};

	state = {
		originX: 0,
		offsetX: 0,
		originY: 0,
		totalY: 0,
		isBounce: false,
		isLoader: false,
		isActive: false,
		validated: this.validateStatus.init,
		isMoving: false,
		isTouchEndSpan: false,
		cptchResult: ''
	};

	maxSlidedWidth = 0;
	ctrlWidth = null;
	sliderWidth = null;

	componentDidMount() {
		document.addEventListener( 'touchend', this.listenMouseUp );
		document.addEventListener( 'mouseup', this.listenMouseUp );
		document.addEventListener( 'mousemove', this.listenMouseMove );
	}

	componentDidUpdate() {
		this.maxSlidedWidth = this.ctrlWidth.clientWidth - this.sliderWidth.clientWidth;
	}

	/* slider element position ( getClientX, getClientY ) */
	getClientX = ( e ) => {
		if ( e.type.indexOf( 'mouse' ) > - 1 ) {
			return e.clientX;
		}
		if ( e.type.indexOf( 'touch' ) > - 1 ) {
			return e.touches[0].clientX;
		}
	};

	getClientY = ( e ) => {
		if ( e.type.indexOf( 'mouse' ) > - 1 ) {
			return e.clientY;
		}
		if ( e.type.indexOf( 'touch' ) > - 1 ) {
			return e.touches[0].clientY;
		}
	};

	/* main slider movement method */
	move = ( e ) => {

		const clientX = this.getClientX(e);
		const clientY = this.getClientY(e);

		let offsetX;
		if ( document.dir === "rtl" ) {
			offsetX = this.state.originX - clientX;
		} else {
			offsetX = clientX - this.state.originX;
		}
		const offsetY = Math.abs(clientY - this.state.originY);

		const totalY = this.state.totalY + offsetY;

		if (offsetX > 0) {

			if ( offsetX > this.maxSlidedWidth ) {
				offsetX = this.maxSlidedWidth;
			}

			this.setState({
				offsetX,
				totalY,
				isMoving: true
			});
		}
	};

	validatedSuccess = () => {

		this.setState( {
			validated: this.validateStatus.success,
		}, () => {

			callback();

			if ( this.props.reset === resetTypeMap.auto ) {

				setTimeout( () => {
					this.resetCaptcha();
				}, 500 );

			}
		} );
	};

	validatedFail = () => {

		this.setState( {
			validated: this.validateStatus.error,
		}, () => {
			callback();

			if ( this.props.reset === resetTypeMap.auto ) {
				setTimeout( () => {
					this.resetCaptcha();
				}, 500 );
			}
		} );
	};

	resetCaptcha = () => {

		/* reset state */
		this.setState( {
			offsetX: 0,
			originX: 0,
			originY: 0,
			totalY: 0,
			isTouchEndSpan: false,
			isMoving: false,
			validated: this.validateStatus.init,
			imgDisplayStatus: this.imgDisplayStatus.hidden
		} );

		if ( this.props.onReset ) {
			this.props.onReset();
		}
	};

	/* mobile handlers */
	handleTouchStart = ( e ) => {
		e.preventDefault();

		if ( this.state.isTouchEndSpan ) {
			return;
		}

		this.handleMoveOver( e );

		this.setState( {
			originX: this.getClientX( e ),
			originY: this.getClientY( e ),
		} );

	};

	handleTouchMove = ( e ) => {
		e.preventDefault();

		if ( this.state.isTouchEndSpan ) {
			return;
		}

		this.move( e );
		this.setState( {
			isMoving: true,
		} );
	};

	handleTouchEnd = ( e ) => {
		if ( this.state.isTouchEndSpan ) {
			return;
		}

		if ( this.state.totalY < (
			this.props.robotValidate && this.props.robotValidate.offsetY || 0
		) ) {

			this.setState( {
				offsetX: 0,
				originX: 0,
				originY: 0,
				totalY: 0,
				isTouchEndSpan: false,
				isMoving: false,
				validated: this.validateStatus.error
			}, () => {
				this.handleMoveOut( e );
				this.props.robotValidate && this.props.robotValidate.handler ? this.props.robotValidate.handler() : alert( '请重试' );
			} );
			return;
		}

		if ( this.state.offsetX > 0 ) {

			const validateValue = this.state.offsetX / this.maxSlidedWidth;

			if ( this.state.offsetX >= this.maxSlidedWidth ) {
				this.setState( {
					isTouchEndSpan: true,
					isLoader: true,
					isMoving: false
				} );

				const querystring = require('querystring');
				const self = this;
				const config = {
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					}
				};

				/* ajax request */
				axios.post( wpSlideCaptcha.ajax_url, querystring.stringify({
					action: 'validate_slide_captcha',
					is_touch_end: 1
				}), config )
		        .then( response => {
					this.state.cptchResult = response.data['slide_captcha_response'];

			        /* stop loader */
			        self.setState( {
				        isLoader: false
			        } );

			        setTimeout( () => {
						this.state.cptchResult = '';
						this.resetCaptcha();
			        }, 10000 );

		        } )
		        .catch( error => {
		        	alert( error );
		        } );
			}

			if ( this.props.onRequest ) {
				this.props.onRequest( validateValue, this.validatedSuccess, this.validatedFail, this.resetCaptcha );
			}
		} else {
			this.resetCaptcha();
		}
	};

	/* desktop handlers */
	handlerMouseDown = ( e ) => {
		e.preventDefault();

		if ( this.state.isTouchEndSpan ) {
			return;
		}
		this.setState( {
			originX: this.getClientX( e ),
			originY: this.getClientY( e ),
			isMoving: true
		} );
	};

	handlerMouseMove = ( e ) => {
		e.preventDefault();

		if ( this.state.isTouchEndSpan ) {
			return;
		}

		if ( this.state.isMoving ) {
			this.move( e );
		}
	};

	handlerMouseUp = ( e ) => {
		e.preventDefault();

		this.handleTouchEnd( e );

		if ( this.state.isTouchEndSpan ) {
			return;
		}
		this.setState( {
			isMoving: false,
			offsetX: 0,
			isBounce: true
		} );
	};

	handleMoveOut = ( e ) => {
		e.preventDefault();

		if ( this.state.imgDisplayStatus === this.imgDisplayStatus.show
		     && this.state.isMoving === false
		     && this.state.validated === this.validateStatus.init
		) {
			this.setState( {
				imgDisplayStatus: this.imgDisplayStatus.hidden
			} );
		}
	};

	handleMoveOver = ( e ) => {
		e.preventDefault();

		if ( this.state.imgDisplayStatus === this.imgDisplayStatus.hidden ) {
			this.setState( {
				imgDisplayStatus: this.imgDisplayStatus.show
			} );
		}
	};

	listenMouseUp = ( e ) => {
		if ( this.state.isMoving === true ) {
			this.handlerMouseUp( e );
		}
	};

	listenMouseMove = ( e ) => {
		this.handlerMouseMove( e );
	};

	setBounce = ( e ) => {
		this.setState( {
			isBounce: false
		} );
	};

    render() {

	   const textSlide = this.state.isTouchEndSpan && ! this.state.isLoader ? wpSlideCaptcha.text_end_slide : wpSlideCaptcha.text_start_slide;

	   let slideAfterElem;
	   if ( this.state.isTouchEndSpan && ! this.state.isLoader ) {
		   slideAfterElem = 'cptch_success';
	   } else if ( this.state.isTouchEndSpan && this.state.isLoader ) {
	   	   slideAfterElem = 'cptch_loading';
	   } else {
	   	   slideAfterElem = 'cptch_slider_arrow';
	   }

		let sliderStyle;
		if ( document.dir === "rtl" ) {
			sliderStyle = { right: this.state.offsetX + 'px' };
		} else {
			sliderStyle = { left: this.state.offsetX + 'px' };
		}

	    return (
		    <div id="cptch_slide_container"
				onMouseMove={this.handlerMouseMove}
				onMouseUp={this.handlerMouseUp}
				ref={(el) => { this.ctrlWidth = el; } }>
			    <div
					id="cptch_slide_slider"
					className={`control ${this.state.isBounce ? 'bounce' : ''} ${this.state.isTouchEndSpan && ! this.state.isLoader ? 'cptch_success' : ''}`}
					onAnimationEnd={this.setBounce}

					ref={(el) => { this.sliderWidth = el; }}
					style={sliderStyle}

				    /* mobile events */
					onTouchStart={this.handleTouchStart}
					onTouchMove={this.handleTouchMove}
					onTouchEnd={this.handleTouchEnd}

				    /* desktop events */
					onMouseDown={this.handlerMouseDown}
					onMouseOver={this.handleMoveOver}
					onMouseOut={this.handleMoveOut}
			    >
                    <span className={slideAfterElem}></span>
			    </div>
			    <p id="slide-title">{textSlide}</p>
				<input type="hidden" name="cptch_result" value={this.state.cptchResult} />
		    </div>
	    );
    }
}

export default App;

