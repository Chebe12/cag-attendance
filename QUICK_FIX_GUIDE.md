# Quick Fix Guide for User Forms

## User Create Form Needed Changes

The current create form at `resources/views/admin/users/create.blade.php` has incorrect field names.

### Required Field Mappings:
1. **Name Fields** (currently has single "name" field):
   ```html
   <input name="firstname" required>
   <input name="middlename">  
   <input name="lastname" required>
   ```

2. **Employee Number** (currently "employee_id"):
   ```html
   <input name="employee_no" required>
   ```

3. **User Type** (currently "role"):
   ```html
   <select name="user_type" required>
     <option value="admin">Admin</option>
     <option value="instructor">Instructor</option>
     <option value="office_staff">Office Staff</option>
   </select>
   ```

4. **Department Dropdown** (NEW - add this):
   ```html
   <select name="department_id">
     <option value="">Select Department</option>
     @foreach(\$departments as \$dept)
       <option value="{{ \$dept->id }}">{{ \$dept->name }}</option>
     @endforeach
   </select>
   ```

5. **Position Field** (add if missing):
   ```html
   <input name="position" required>
   ```

## Same changes apply to Edit Form

---

## Recurring Schedules Feature

Update Schedule Create/Edit forms:

1. Add checkbox:
   ```html
   <input type="checkbox" name="is_recurring" value="1">
   ```

2. Add day of week:
   ```html
   <select name="day_of_week">
     <option value="monday">Monday</option>
     <option value="tuesday">Tuesday</option>
     ...
   </select>
   ```

3. Update ScheduleController@store to create recurring schedules when checked.
