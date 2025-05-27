var $ = jQuery;

var Modal = function Modal() {
    var self = this;
    
    // Error Modal
    this.error = function (params) {
        // Create the modal container
        var container = $('<div></div>');
        
        // Create section to hold title, message and action
        var section = $('<section></section>');
        
        // Create header to hold title
        var header = $('<h1></h1>');
        
        // Create paragraph to hold mesage
        var message = $('<p></p>').html(params.message);
        
        // Create action section
        var action = $('<div></div>');
        
        // Create title
        var title = $('<span></span>').addClass('qnap-title-red').text(params.title);
        
        // Create close button
        var closeButton = $('<button type="button" class="qnap-button-red"></button>').on('click', function () {
            self.destroy();
        });
        
        // Append text to close button
        closeButton.append(qnap_locale.close_export);
        
        // Append close button to action
        action.append(closeButton);
        
        // Append title to section
        header.append(title);
        
        // Append header and message to section
        section.append(header).append(message);
        
        // Append section and action to container
        container.append(section).append(action);
        
        // Render modal
        self.modal.html(container).show();
        self.modal.trigger('focus');
        self.overlay.show();
    };
    
    // Progress Modal
    this.progress = function (params) {
        // Update progress bar meter
        if (this.progress.progressBarMeter) {
            this.progress.progressBarMeter.width(params.percent + '%');
        }
        
        // Update progress bar percent
        if (this.progress.progressBarPercent) {
            this.progress.progressBarPercent.text(params.percent + '%');
            
            // Update progress message if provided
            if (params.message && this.progress.messageDisplay) {
                this.progress.messageDisplay.html(params.message);
            }
            return;
        }
        
        // Create the modal container
        var container = $('<div></div>');
        
        // Create section to hold title, message and action
        var section = $('<section></section>');
        
        // Create header to hold progress bar
        var header = $('<h1></h1>');
        
        // Create div for message display
        this.progress.messageDisplay = $('<div class="qnap-progress-status"></div>');
        if (params.message) {
            this.progress.messageDisplay.html(params.message);
        }
        
        // Create action section
        var action = $('<div></div>');
        
        // Create progress container
        var progressContainer = $('<div class="qnap-progress-container"></div>');
        
        // Create progress bar
        var progressBar = $('<div class="qnap-progress-bar qnap-progress-bar-animated"></div>').width(params.percent + '%');
        
        // Store references for later updates
        this.progress.progressBarMeter = progressBar;
        this.progress.progressBarPercent = $('<span class="qnap-progress-percentage"></span>').text(params.percent + '%');
        
        // Create progress info section
        var progressInfo = $('<div class="qnap-progress-info"></div>');
        progressInfo.append(this.progress.progressBarPercent);
        
        // Create stop export
        var stopButton = $('<button type="button" class="qnap-button-red"></button>').on('click', function () {
            stopButton.attr('disabled', 'disabled');
            self.onStop();
        });
        
        // Append text to stop button
        stopButton.append('<i class="qnap-icon-notification"></i> ' + qnap_locale.stop_export);
        
        // Append progress bar to container
        progressContainer.append(progressBar);
        
        // Append message, progress container, and progress info
        section.append(this.progress.messageDisplay)
               .append(progressContainer)
               .append(progressInfo);
        
        // Append stop button to action
        action.append(stopButton);
        
        // Append section and action to container
        container.append(section).append(action);
        
        // Render modal
        self.modal.html(container).show();
        self.modal.trigger('focus');
        self.overlay.show();
    };
    
    // Info Modal
    this.info = function (params) {
        // Create the modal container
        var container = $('<div></div>');
        
        // Create section to hold title, message and action
        var section = $('<section></section>');
        
        // Create header to hold loader
        var header = $('<h1></h1>');
        
        // Create paragraph to hold mesage
        var message = $('<p></p>').html(params.message);
        
        // Create action section
        var action = $('<div></div>');
        
        // Create loader
        var loader = $('<span class="qnap-loader"></span>');
        
        // Create stop export
        var stopButton = $('<button type="button" class="qnap-button-red"></button>').on('click', function () {
            stopButton.attr('disabled', 'disabled');
            self.onStop();
        });
        
        // Append text to stop button
        stopButton.append('<i class="qnap-icon-notification"></i> ' + qnap_locale.stop_export);
        
        // Append stop button to action
        action.append(stopButton);
        
        // Append loader to header
        header.append(loader);
        
        // Append header and message to section
        section.append(header).append(message);
        
        // Append section and action to container
        container.append(section).append(action);
        
        // Render modal
        self.modal.html(container).show();
        self.modal.trigger('focus');
        self.overlay.show();
    };
    
    // Done Modal
    this.done = function (params) {
        // Create the modal container
        var container = $('<div></div>');
        
        // Create section to hold title, message and action
        var section = $('<section></section>');
        
        // Create header to hold title
        var header = $('<h1></h1>');
        
        // Create paragraph to hold mesage
        var message = $('<p></p>').html(params.message);
        
        // Create action section
        var action = $('<div></div>');
        
        // Create title
        var title = $('<span></span>').addClass('qnap-title-green').text(params.title);
        
        // Create close button
        var closeButton = $('<button type="button" class="qnap-button-red"></button>').on('click', function () {
            self.destroy();
        });
        
        // Append text to close button
        closeButton.append(qnap_locale.close_export);
        
        // Append close button to action
        action.append(closeButton);
        
        // Append title to section
        header.append(title);
        
        // Append header and message to section
        section.append(header).append(message);
        
        // Append section and action to container
        container.append(section).append(action);
        
        // Render modal
        self.modal.html(container).show();
        self.modal.trigger('focus');
        self.overlay.show();
    };
    
    // Download Modal
    this.download = function (params) {
        // Create the modal container
        var container = $('<div></div>');
        
        // Create section to hold title, message and action
        var section = $('<section></section>');
        
        // Create paragraph to hold mesage
        var message = $('<p></p>').html(params.message);
        
        // Create action section
        var action = $('<div></div>');
        
        // Create close button
        var closeButton = $('<button type="button" class="qnap-button-red"></button>').on('click', function () {
            self.destroy();
        });
        
        // Append text to close button
        closeButton.append(qnap_locale.close_export);
        
        // Append close button to action
        action.append(closeButton);
        
        // Append message to section
        section.append(message);
        
        // Append section and action to container
        container.append(section).append(action);
        
        // Render modal
        self.modal.html(container).show();
        self.modal.trigger('focus');
        self.overlay.show();
    };
    
    // Create the overlay
    this.overlay = $('<div class="qnap-overlay"></div>');
    
    // Create the modal container
    this.modal = $('<div class="qnap-modal-container" role="dialog" tabindex="-1"></div>');
    
    $('body').append(this.overlay) // Append overlay to body
             .append(this.modal); // Append modal to body
};

Modal.prototype.render = function (params) {
    $(document).trigger('qnap-export-status', params);
    
    // Show modal
    switch (params.type) {
        case 'error':
            this.error(params);
            break;
            
        case 'info':
            this.info(params);
            break;
            
        case 'progress':
            this.progress(params);
            break;
            
        case 'done':
            this.done(params);
            break;
            
        case 'download':
            this.download(params);
            break;
    }
};

Modal.prototype.destroy = function () {
    this.modal.hide();
    this.overlay.hide();
};

module.exports = Modal;