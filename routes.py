import json
import base64
from datetime import datetime, timedelta
from io import BytesIO

from flask import render_template, request, redirect, url_for, flash, jsonify, session
from flask_login import login_user, logout_user, login_required, current_user
from werkzeug.security import generate_password_hash, check_password_hash

from app import app, db
from models import User, Medication, Appointment, Prescription, EmergencyContact, Notification


@app.route('/')
def index():
    if current_user.is_authenticated:
        # Get upcoming appointments
        upcoming_appointments = Appointment.query.filter_by(user_id=current_user.id).filter(
            Appointment.date >= datetime.now().date()
        ).order_by(Appointment.date, Appointment.time).limit(3).all()
        
        # Get medications due today
        medications = Medication.query.filter_by(user_id=current_user.id).all()
        
        # Get unread notifications
        notifications = Notification.query.filter_by(
            user_id=current_user.id, 
            is_read=False
        ).order_by(Notification.scheduled_time).all()
        
        return render_template(
            'index.html', 
            upcoming_appointments=upcoming_appointments,
            medications=medications,
            notifications=notifications
        )
    return render_template('index.html')


@app.route('/register', methods=['GET', 'POST'])
def register():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    
    if request.method == 'POST':
        username = request.form.get('username')
        email = request.form.get('email')
        password = request.form.get('password')
        password_confirm = request.form.get('password_confirm')
        
        # Validate input
        if not username or not email or not password:
            flash('All fields are required', 'danger')
            return redirect(url_for('register'))
        
        if password != password_confirm:
            flash('Passwords do not match', 'danger')
            return redirect(url_for('register'))
        
        # Check if username or email already exists
        if User.query.filter_by(username=username).first():
            flash('Username already exists', 'danger')
            return redirect(url_for('register'))
        
        if User.query.filter_by(email=email).first():
            flash('Email already exists', 'danger')
            return redirect(url_for('register'))
        
        # Create new user
        user = User(username=username, email=email)
        user.set_password(password)
        
        db.session.add(user)
        db.session.commit()
        
        flash('Registration successful! Please log in.', 'success')
        return redirect(url_for('login'))
    
    return render_template('register.html')


@app.route('/login', methods=['GET', 'POST'])
def login():
    if current_user.is_authenticated:
        return redirect(url_for('index'))
    
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        remember_me = 'remember_me' in request.form
        
        # Validate input
        if not username or not password:
            flash('Username and password are required', 'danger')
            return redirect(url_for('login'))
        
        # Check if user exists
        user = User.query.filter_by(username=username).first()
        if not user or not user.check_password(password):
            flash('Invalid username or password', 'danger')
            return redirect(url_for('login'))
        
        # Log in user
        login_user(user, remember=remember_me)
        flash(f'Welcome back, {user.username}!', 'success')
        
        # Set theme preference
        session['theme'] = user.theme_preference
        
        # Redirect to requested page or default to index
        next_page = request.args.get('next')
        if not next_page or not next_page.startswith('/'):
            next_page = url_for('index')
        return redirect(next_page)
    
    return render_template('login.html')


@app.route('/logout')
@login_required
def logout():
    logout_user()
    flash('You have been logged out.', 'info')
    return redirect(url_for('index'))


@app.route('/set_theme', methods=['POST'])
@login_required
def set_theme():
    theme = request.form.get('theme', 'light')
    if theme not in ['light', 'dark']:
        theme = 'light'
    
    # Update user preference in database
    current_user.theme_preference = theme
    db.session.commit()
    
    # Update session
    session['theme'] = theme
    
    return redirect(request.referrer or url_for('index'))


# Medication routes
@app.route('/medications')
@login_required
def medications():
    medications = Medication.query.filter_by(user_id=current_user.id).order_by(Medication.name).all()
    return render_template('medications.html', medications=medications)


@app.route('/api/medications', methods=['GET'])
@login_required
def get_medications():
    medications = Medication.query.filter_by(user_id=current_user.id).all()
    result = []
    
    for medication in medications:
        result.append({
            'id': medication.id,
            'name': medication.name,
            'dosage': medication.dosage,
            'frequency': medication.frequency,
            'time': medication.time,
            'notes': medication.notes
        })
    
    return jsonify(result)


@app.route('/api/medications', methods=['POST'])
@login_required
def add_medication():
    data = request.json
    if not data or not data.get('name') or not data.get('dosage') or not data.get('frequency') or not data.get('time'):
        return jsonify({'error': 'Incomplete medication data'}), 400
    
    try:
        medication = Medication(
            name=data['name'],
            dosage=data['dosage'],
            frequency=data['frequency'],
            time=json.dumps(data['time']),
            notes=data.get('notes', ''),
            user_id=current_user.id
        )
        
        db.session.add(medication)
        db.session.commit()
        
        # Create notifications for this medication
        create_medication_notifications(medication)
        
        return jsonify({
            'id': medication.id,
            'name': medication.name,
            'dosage': medication.dosage,
            'frequency': medication.frequency,
            'time': medication.time,
            'notes': medication.notes
        }), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/medications/<int:id>', methods=['PUT'])
@login_required
def update_medication(id):
    medication = Medication.query.filter_by(id=id, user_id=current_user.id).first()
    if not medication:
        return jsonify({'error': 'Medication not found'}), 404
    
    data = request.json
    if not data:
        return jsonify({'error': 'No data provided'}), 400
    
    try:
        if 'name' in data:
            medication.name = data['name']
        if 'dosage' in data:
            medication.dosage = data['dosage']
        if 'frequency' in data:
            medication.frequency = data['frequency']
        if 'time' in data:
            medication.time = json.dumps(data['time'])
        if 'notes' in data:
            medication.notes = data['notes']
        
        db.session.commit()
        
        # Update notifications for this medication
        # First, delete existing notifications
        Notification.query.filter_by(
            user_id=current_user.id,
            type='medication',
            item_id=medication.id
        ).delete()
        db.session.commit()
        
        # Then create new ones
        create_medication_notifications(medication)
        
        return jsonify({
            'id': medication.id,
            'name': medication.name,
            'dosage': medication.dosage,
            'frequency': medication.frequency,
            'time': medication.time,
            'notes': medication.notes
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/medications/<int:id>', methods=['DELETE'])
@login_required
def delete_medication(id):
    medication = Medication.query.filter_by(id=id, user_id=current_user.id).first()
    if not medication:
        return jsonify({'error': 'Medication not found'}), 404
    
    try:
        # Delete associated notifications
        Notification.query.filter_by(
            user_id=current_user.id,
            type='medication',
            item_id=medication.id
        ).delete()
        
        # Delete medication
        db.session.delete(medication)
        db.session.commit()
        
        return jsonify({'message': 'Medication deleted successfully'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


# Appointment routes
@app.route('/appointments')
@login_required
def appointments():
    appointments = Appointment.query.filter_by(user_id=current_user.id).order_by(Appointment.date, Appointment.time).all()
    return render_template('appointments.html', appointments=appointments)


@app.route('/api/appointments', methods=['GET'])
@login_required
def get_appointments():
    appointments = Appointment.query.filter_by(user_id=current_user.id).all()
    result = []
    
    for appointment in appointments:
        result.append({
            'id': appointment.id,
            'title': appointment.title,
            'doctor': appointment.doctor,
            'location': appointment.location,
            'date': appointment.date.strftime('%Y-%m-%d'),
            'time': appointment.time.strftime('%H:%M'),
            'notes': appointment.notes
        })
    
    return jsonify(result)


@app.route('/api/appointments', methods=['POST'])
@login_required
def add_appointment():
    data = request.json
    if not data or not data.get('title') or not data.get('date') or not data.get('time'):
        return jsonify({'error': 'Incomplete appointment data'}), 400
    
    try:
        date_obj = datetime.strptime(data['date'], '%Y-%m-%d').date()
        time_obj = datetime.strptime(data['time'], '%H:%M').time()
        
        appointment = Appointment(
            title=data['title'],
            doctor=data.get('doctor', ''),
            location=data.get('location', ''),
            date=date_obj,
            time=time_obj,
            notes=data.get('notes', ''),
            user_id=current_user.id
        )
        
        db.session.add(appointment)
        db.session.commit()
        
        # Create notification for this appointment
        create_appointment_notification(appointment)
        
        return jsonify({
            'id': appointment.id,
            'title': appointment.title,
            'doctor': appointment.doctor,
            'location': appointment.location,
            'date': appointment.date.strftime('%Y-%m-%d'),
            'time': appointment.time.strftime('%H:%M'),
            'notes': appointment.notes
        }), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/appointments/<int:id>', methods=['PUT'])
@login_required
def update_appointment(id):
    appointment = Appointment.query.filter_by(id=id, user_id=current_user.id).first()
    if not appointment:
        return jsonify({'error': 'Appointment not found'}), 404
    
    data = request.json
    if not data:
        return jsonify({'error': 'No data provided'}), 400
    
    try:
        if 'title' in data:
            appointment.title = data['title']
        if 'doctor' in data:
            appointment.doctor = data['doctor']
        if 'location' in data:
            appointment.location = data['location']
        if 'date' in data:
            appointment.date = datetime.strptime(data['date'], '%Y-%m-%d').date()
        if 'time' in data:
            appointment.time = datetime.strptime(data['time'], '%H:%M').time()
        if 'notes' in data:
            appointment.notes = data['notes']
        
        db.session.commit()
        
        # Update notification for this appointment
        # First, delete existing notification
        Notification.query.filter_by(
            user_id=current_user.id,
            type='appointment',
            item_id=appointment.id
        ).delete()
        db.session.commit()
        
        # Then create a new one
        create_appointment_notification(appointment)
        
        return jsonify({
            'id': appointment.id,
            'title': appointment.title,
            'doctor': appointment.doctor,
            'location': appointment.location,
            'date': appointment.date.strftime('%Y-%m-%d'),
            'time': appointment.time.strftime('%H:%M'),
            'notes': appointment.notes
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/appointments/<int:id>', methods=['DELETE'])
@login_required
def delete_appointment(id):
    appointment = Appointment.query.filter_by(id=id, user_id=current_user.id).first()
    if not appointment:
        return jsonify({'error': 'Appointment not found'}), 404
    
    try:
        # Delete associated notification
        Notification.query.filter_by(
            user_id=current_user.id,
            type='appointment',
            item_id=appointment.id
        ).delete()
        
        # Delete appointment
        db.session.delete(appointment)
        db.session.commit()
        
        return jsonify({'message': 'Appointment deleted successfully'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


# Prescription routes
@app.route('/prescriptions')
@login_required
def prescriptions():
    prescriptions = Prescription.query.filter_by(user_id=current_user.id).order_by(Prescription.date.desc()).all()
    return render_template('prescriptions.html', prescriptions=prescriptions)


@app.route('/api/prescriptions', methods=['GET'])
@login_required
def get_prescriptions():
    prescriptions = Prescription.query.filter_by(user_id=current_user.id).all()
    result = []
    
    for prescription in prescriptions:
        result.append({
            'id': prescription.id,
            'title': prescription.title,
            'doctor': prescription.doctor,
            'date': prescription.date.strftime('%Y-%m-%d'),
            'image_data': prescription.image_data,
            'notes': prescription.notes
        })
    
    return jsonify(result)


@app.route('/api/prescriptions', methods=['POST'])
@login_required
def add_prescription():
    if 'title' not in request.form or 'date' not in request.form:
        return jsonify({'error': 'Title and date are required'}), 400
    
    try:
        title = request.form.get('title')
        doctor = request.form.get('doctor', '')
        date_str = request.form.get('date')
        notes = request.form.get('notes', '')
        
        date_obj = datetime.strptime(date_str, '%Y-%m-%d').date()
        
        # Handle image file
        image_data = None
        if 'image' in request.files:
            image_file = request.files['image']
            if image_file.filename:
                # Read and encode image
                image_data_bytes = BytesIO()
                image_file.save(image_data_bytes)
                image_data = base64.b64encode(image_data_bytes.getvalue()).decode('utf-8')
        
        prescription = Prescription(
            title=title,
            doctor=doctor,
            date=date_obj,
            image_data=image_data,
            notes=notes,
            user_id=current_user.id
        )
        
        db.session.add(prescription)
        db.session.commit()
        
        return jsonify({
            'id': prescription.id,
            'title': prescription.title,
            'doctor': prescription.doctor,
            'date': prescription.date.strftime('%Y-%m-%d'),
            'image_data': prescription.image_data,
            'notes': prescription.notes
        }), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/prescriptions/<int:id>', methods=['PUT'])
@login_required
def update_prescription(id):
    prescription = Prescription.query.filter_by(id=id, user_id=current_user.id).first()
    if not prescription:
        return jsonify({'error': 'Prescription not found'}), 404
    
    try:
        if 'title' in request.form:
            prescription.title = request.form.get('title')
        if 'doctor' in request.form:
            prescription.doctor = request.form.get('doctor')
        if 'date' in request.form:
            prescription.date = datetime.strptime(request.form.get('date'), '%Y-%m-%d').date()
        if 'notes' in request.form:
            prescription.notes = request.form.get('notes')
        
        # Handle image file
        if 'image' in request.files:
            image_file = request.files['image']
            if image_file.filename:
                # Read and encode image
                image_data_bytes = BytesIO()
                image_file.save(image_data_bytes)
                prescription.image_data = base64.b64encode(image_data_bytes.getvalue()).decode('utf-8')
        
        db.session.commit()
        
        return jsonify({
            'id': prescription.id,
            'title': prescription.title,
            'doctor': prescription.doctor,
            'date': prescription.date.strftime('%Y-%m-%d'),
            'image_data': prescription.image_data,
            'notes': prescription.notes
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/prescriptions/<int:id>', methods=['DELETE'])
@login_required
def delete_prescription(id):
    prescription = Prescription.query.filter_by(id=id, user_id=current_user.id).first()
    if not prescription:
        return jsonify({'error': 'Prescription not found'}), 404
    
    try:
        db.session.delete(prescription)
        db.session.commit()
        
        return jsonify({'message': 'Prescription deleted successfully'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


# Emergency Contact routes
@app.route('/contacts')
@login_required
def contacts():
    contacts = EmergencyContact.query.filter_by(user_id=current_user.id).order_by(EmergencyContact.name).all()
    return render_template('contacts.html', contacts=contacts)


@app.route('/api/contacts', methods=['GET'])
@login_required
def get_contacts():
    contacts = EmergencyContact.query.filter_by(user_id=current_user.id).all()
    result = []
    
    for contact in contacts:
        result.append({
            'id': contact.id,
            'name': contact.name,
            'relationship': contact.relationship,
            'phone': contact.phone,
            'email': contact.email,
            'notes': contact.notes
        })
    
    return jsonify(result)


@app.route('/api/contacts', methods=['POST'])
@login_required
def add_contact():
    data = request.json
    if not data or not data.get('name') or not data.get('phone'):
        return jsonify({'error': 'Name and phone number are required'}), 400
    
    try:
        contact = EmergencyContact(
            name=data['name'],
            relationship=data.get('relationship', ''),
            phone=data['phone'],
            email=data.get('email', ''),
            notes=data.get('notes', ''),
            user_id=current_user.id
        )
        
        db.session.add(contact)
        db.session.commit()
        
        return jsonify({
            'id': contact.id,
            'name': contact.name,
            'relationship': contact.relationship,
            'phone': contact.phone,
            'email': contact.email,
            'notes': contact.notes
        }), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/contacts/<int:id>', methods=['PUT'])
@login_required
def update_contact(id):
    contact = EmergencyContact.query.filter_by(id=id, user_id=current_user.id).first()
    if not contact:
        return jsonify({'error': 'Contact not found'}), 404
    
    data = request.json
    if not data:
        return jsonify({'error': 'No data provided'}), 400
    
    try:
        if 'name' in data:
            contact.name = data['name']
        if 'relationship' in data:
            contact.relationship = data['relationship']
        if 'phone' in data:
            contact.phone = data['phone']
        if 'email' in data:
            contact.email = data['email']
        if 'notes' in data:
            contact.notes = data['notes']
        
        db.session.commit()
        
        return jsonify({
            'id': contact.id,
            'name': contact.name,
            'relationship': contact.relationship,
            'phone': contact.phone,
            'email': contact.email,
            'notes': contact.notes
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/contacts/<int:id>', methods=['DELETE'])
@login_required
def delete_contact(id):
    contact = EmergencyContact.query.filter_by(id=id, user_id=current_user.id).first()
    if not contact:
        return jsonify({'error': 'Contact not found'}), 404
    
    try:
        db.session.delete(contact)
        db.session.commit()
        
        return jsonify({'message': 'Contact deleted successfully'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


# Notification routes
@app.route('/api/notifications', methods=['GET'])
@login_required
def get_notifications():
    notifications = Notification.query.filter_by(
        user_id=current_user.id
    ).order_by(Notification.scheduled_time).all()
    
    result = []
    for notification in notifications:
        result.append({
            'id': notification.id,
            'type': notification.type,
            'item_id': notification.item_id,
            'scheduled_time': notification.scheduled_time.isoformat(),
            'message': notification.message,
            'is_read': notification.is_read
        })
    
    return jsonify(result)


@app.route('/api/notifications/unread', methods=['GET'])
@login_required
def get_unread_notifications():
    notifications = Notification.query.filter_by(
        user_id=current_user.id,
        is_read=False
    ).order_by(Notification.scheduled_time).all()
    
    result = []
    for notification in notifications:
        result.append({
            'id': notification.id,
            'type': notification.type,
            'item_id': notification.item_id,
            'scheduled_time': notification.scheduled_time.isoformat(),
            'message': notification.message,
            'is_read': notification.is_read
        })
    
    return jsonify(result)


@app.route('/api/notifications/<int:id>/mark_read', methods=['POST'])
@login_required
def mark_notification_read(id):
    notification = Notification.query.filter_by(id=id, user_id=current_user.id).first()
    if not notification:
        return jsonify({'error': 'Notification not found'}), 404
    
    try:
        notification.is_read = True
        db.session.commit()
        
        return jsonify({'message': 'Notification marked as read'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


@app.route('/api/notifications/mark_all_read', methods=['POST'])
@login_required
def mark_all_notifications_read():
    try:
        notifications = Notification.query.filter_by(
            user_id=current_user.id,
            is_read=False
        ).all()
        
        for notification in notifications:
            notification.is_read = True
        
        db.session.commit()
        
        return jsonify({'message': 'All notifications marked as read'})
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 500


# Utility functions for notifications
def create_medication_notifications(medication):
    """Create notifications for a medication."""
    try:
        # Parse the time JSON
        time_slots = json.loads(medication.time)
        
        # Create a notification for each time slot for today
        today = datetime.now().date()
        for time_str in time_slots:
            time_parts = time_str.split(':')
            hour = int(time_parts[0])
            minute = int(time_parts[1])
            
            notification_time = datetime.combine(today, datetime.min.time()) + timedelta(hours=hour, minutes=minute)
            
            # Skip if the time has already passed for today
            if notification_time < datetime.now():
                continue
            
            message = f"Time to take {medication.name} - {medication.dosage}"
            
            notification = Notification(
                type='medication',
                item_id=medication.id,
                scheduled_time=notification_time,
                message=message,
                user_id=medication.user_id
            )
            
            db.session.add(notification)
        
        db.session.commit()
    except Exception as e:
        db.session.rollback()
        app.logger.error(f"Error creating medication notifications: {str(e)}")


def create_appointment_notification(appointment):
    """Create a notification for an appointment."""
    try:
        # Create notification for 1 day before the appointment
        appointment_datetime = datetime.combine(appointment.date, appointment.time)
        notification_time = appointment_datetime - timedelta(days=1)
        
        # Skip if the notification time has already passed
        if notification_time < datetime.now():
            return
        
        message = f"Reminder: {appointment.title} tomorrow at {appointment.time.strftime('%H:%M')}"
        if appointment.location:
            message += f" at {appointment.location}"
        
        notification = Notification(
            type='appointment',
            item_id=appointment.id,
            scheduled_time=notification_time,
            message=message,
            user_id=appointment.user_id
        )
        
        db.session.add(notification)
        db.session.commit()
    except Exception as e:
        db.session.rollback()
        app.logger.error(f"Error creating appointment notification: {str(e)}")


@app.context_processor
def inject_template_vars():
    """Inject variables into all templates."""
    # Add theme preference
    if current_user.is_authenticated:
        theme = session.get('theme', current_user.theme_preference)
    else:
        theme = session.get('theme', 'light')
    
    # Add current year for copyright
    current_year = datetime.now().year
    
    return {
        'theme': theme,
        'current_year': current_year
    }
